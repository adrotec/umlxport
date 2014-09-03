<?php

class ExportSettings {
    
    const ACTION_NONE = 'none';
    const ACTION_IMPORT = 'import';
    const ACTION_GENERATE = 'generate';

//    public $settings->baseDirectory = '/media/adrotec/uml-importer/test/App1';
//    public $baseDirectory = '/var/www/MyApplication/src/TestBundle/Resources/config/doctrine';
//    public $namespace = 'HelloWorld\App1\Entity';
    public $namespace = 'Acme\TestBundle\Entity';
    public $format = 'xml';
    public $fileNamePattern = '{namespace}.{class}.{extension}';
    public $hideForm = false;
    public $mappings = array();
    public $generated = array();
    public $errors = array();
    public $existingAlready = array();
    public $fileNameCssClasses = array();
    public $fileNameMessages = array();
    public $fileNames = array();
    public $action = self::ACTION_NONE;
    
    public $dbNamingConvention = 'snake_case';
    
    public $uml;


//    public function toArray(){
//        $arr = array();
//        foreach($this as $key => $value){
//            $arr[$key] = $value;
//        }
//        return $arr;
//    }
    
}

$settings = new ExportSettings();

if($request->getMethod() === 'POST'){   
    foreach($settings as $key => $defaultVal){
        if(property_exists($settings, $key)){
            $settings->$key = $request->request->get($key);
        }
    }
}

function getFileName($class, $settings){
//    global $settings;
//    return '';
    $classShortName = strtr(trim(str_replace($settings->namespace, '', $class), '\\'), '\\', '.');
    
    $extension = $settings->format;
    
    if($settings->format == 'annotation'){
        $extension = 'php';
    }
    if($settings->format == 'xml'){
        if(strpos($settings->fileNamePattern, '{namespace}') !== false){
            $extension = 'dcm.xml';
        }
        else {
            // possible symfony 2
            $extension = 'orm.xml';
        }
    }
    
//    $extension = '.'.$extension;
    
    $file = strtr($settings->fileNamePattern, array(
        '{namespace}' => trim(strtr($settings->namespace, '\\', '.'), '.'),
        '{class}' => $classShortName,
        '{extension}' => $extension
    ));
    
//    $file = $settings->baseDirectory . DIRECTORY_SEPARATOR . $file;
    
    return $file;
}

if($settings->action == ExportSettings::ACTION_IMPORT && $umlFile = $request->files->get('umlFile')){
    /* @var $umlFile \Symfony\Component\HttpFoundation\File\UploadedFile */
    if($umlFile->isValid()){
        $settings->uml = file_get_contents($umlFile->getPathname());
    }
}

if($settings->action == ExportSettings::ACTION_IMPORT || $settings->action == ExportSettings::ACTION_GENERATE) {

        $processor = new \Adrotec\UmlXport\Processor\UmlProcessor();
        $classes = $processor->process($settings->uml);

        $allMappings = array();

        $mappingConverter = new \Adrotec\UmlXport\Doctrine\ORM\MappingConverter();

        if($settings->dbNamingConvention){
            if($settings->dbNamingConvention == 'snake_case'){
                $mappingConverter->setDbNamingStrategy(new \Adrotec\UmlXport\Doctrine\NamingStrategy\SnakeCaseNamingStrategy());
            }
            else if($settings->dbNamingConvention == 'CamelCase'){
                $mappingConverter->setDbNamingStrategy(new \Adrotec\UmlXport\Doctrine\NamingStrategy\CamelCaseNamingStrategy());
            }
            else if($settings->dbNamingConvention == 'camelCase'){
                $mappingConverter->setDbNamingStrategy(new \Adrotec\UmlXport\Doctrine\NamingStrategy\LowerCamelCaseNamingStrategy());
            }
            else if($settings->dbNamingConvention == 'same'){
                $mappingConverter->setDbNamingStrategy(new Adrotec\UmlXport\Doctrine\NamingStrategy\SameNamingStrategy());
            }
        }
        
        $exporter = new \Adrotec\UmlXport\Doctrine\ORM\UmlExporter($mappingConverter);

        $exporter->setFormat($settings->format);

        foreach ($classes as $umlClass) {
            $umlClass->setNamespace($settings->namespace);
            $mappingCode = $exporter->export($umlClass);
            if($mappingCode){
                $class = $umlClass->getFullyQualifiedName();
                $settings->fileNames[$class] = getFileName($class, $settings);
                
                if(file_exists($settings->fileNames[$class])){
                    $settings->fileNameCssClasses[$class] = 'file-name-existing-warning';
                    $settings->fileNameMessages[$class] = 'FILE EXISTS: ';
                }

                $allMappings[$class] = $mappingCode;
            }
        }
        $settings->mappings = $allMappings;

//        $settings->hideForm = true;

//    }
}
    
if($settings->action == ExportSettings::ACTION_GENERATE && !empty($settings->mappings)){

//    $mapping = $request->request->get('mapping');
//
//    echo '<pre>';
//    print_r($settings->mappings); exit;
    
    $zip = new \ZipArchive();
    $zipFile = tempnam(sys_get_temp_dir(), 'IMPORT_ZIP_');
    $zipped = $zip->open($zipFile, \ZipArchive::CREATE);

    foreach($settings->mappings as $class => $mappingCode){
//            $class = strtr($class, '$', '\\');
        $file = getFileName($class, $settings);
        $settings->fileNames[$class] = $file;
        $fileExists = file_exists($settings->fileNames[$class]);
        
        if($zipped == true){
            $zip->addFromString($file, $mappingCode);
        } else
        
        if ($fileExists) {
            $settings->fileNameCssClasses[$class] = 'file-name-existing';
            $settings->fileNameMessages[$class] = 'FILE EXISTS: ';
//            $content .= '<span style="color: red">FILE EXISTS: "' . $file . '"</span>';
        } else {
            if (@file_put_contents($file, $mappingCode)) {
                $settings->fileNameCssClasses[$class] = 'file-name-new';
                $settings->fileNameMessages[$class] = 'FILE CREATED: ';
            } else {
                $settings->fileNameCssClasses[$class] = 'file-name-existing';
                $settings->fileNameMessages[$class] = 'ERROR: Failed to write to file: ';
//                $content .= '<span style="color: red">ERROR: Failed to write to file: "' . $file . '"</span><br>';
            }
//                    echo $file.'<br><textarea>'.$mapping.'</textarea><br>';
        }
    }

    if($zipped){
        $zip->close();
        sendFile($zipFile, 'mappings.zip');
    }
//    $settings->hideForm = true;

}

function sendFile($filePath, $fileName){
    // send $filename to browser
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    $size = filesize($filePath);

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        // cache settings for IE6 on HTTPS
        header('Cache-Control: max-age=120');
        header('Pragma: public');
    } else {
        header('Cache-Control: private, max-age=120, must-revalidate');
        header("Pragma: no-cache");
    }
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // long ago
    header("Content-Type: $mimeType");
    header('Content-Disposition: attachment; filename="' . $fileName . '";');
    header("Accept-Ranges: bytes");
    header('Content-Length: ' . $size);

    print readfile($filePath);
    exit;
}

//$arr = ($settings->toArray());
//
//extract($arr);

$settings->hideForm = !empty($settings->mappings);

if(empty($settings->fileNames)){
    foreach($settings->mappings as $class => $mappingCode){
        $settings->fileNames[$class] = getFileName($class, $settings);
    }
}