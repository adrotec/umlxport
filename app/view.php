<button class="toggle-uml-settings btn btn-default btn-sm">Toggle form</button>
<form method="post" enctype="multipart/form-data">
    <section class="uml-settings <?php echo $settings->hideForm ? 'hidden' : '' ?>">
        <div class="form-group row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>UML File <small class="text-muted">*.xmi, *.xml</small></label>
                    <input class="form-control" type="file" name="umlFile" accept="text/plain,text/xml,.xml,.xmi">
                </div>
            </div>
            <div class="col-md-6">
                <!--<label class="or-separator">or</label>-->
                <div class="form-group">
                    <label>UML Content</label>
                    <textarea class="form-control" name="uml"><?php echo $settings->uml; ?></textarea>
                </div>
            </div>
        </div>

        <?php if(isset($settings->baseDirectory)) { ?>
            <div class="form-group">
                <label>Base Directory</label>
                <input class="form-control" type="text" name="baseDirectory"
                       placeholder="<?php echo $settings->baseDirectory; ?>" value="<?php echo $settings->baseDirectory; ?>">
            </div>
        <?php } ?>
        <div class="form-group">
            <label>Namespace</label>
            <input class="form-control" type="text" name="namespace"
                   placeholder="<?php echo $settings->namespace; ?>" value="<?php echo $settings->namespace; ?>">
        </div>
        <div class="form-group">
            <label>Format</label>
            <div class="controls">
                <?php
                foreach (array('annotation' => 'Annotation', 'xml' => 'XML', 'yml' => 'YAML', 'php' => 'PHP')
                as $value => $label) {
                    ?>
                    <label class="radio-inline">
                        <input type="radio" name="format" value="<?php echo $value; ?>" <?php echo $settings->format == $value ? 'checked' : ''; ?>> <?php echo $label; ?>
                    </label>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label>File Name pattern</label>
            <input class="form-control" type="text" name="fileNamePattern"
                   placeholder="<?php echo $settings->fileNamePattern; ?>" value="<?php echo $settings->fileNamePattern; ?>">
        </div>
        <div class="form-group">
            <label>Name database columns and tables</label>
            <div class="controls">
                <?php
                foreach (array('camelCase' => 'likeThis', 'snake_case' => 'like_this', 'CamelCase' => 'LikeThis', 'same' => 'Same as Field Name')
                as $value => $label) {
                    ?>
                    <label class="radio-inline">
                        <input type="radio" name="dbNamingConvention" value="<?php echo $value; ?>" <?php echo $settings->dbNamingConvention == $value ? 'checked' : ''; ?>> <?php echo $label; ?>
                    </label>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <button class="btn-reset btn btn-default btn-block should-confirm" type="reset">Reset</button>
            <button class="btn-submit btn btn-primary btn-block" type="submit" name="action" value="import">Submit</button>
        </div>
    </section>
    <section style="<?php echo!$settings->hideForm ? 'display: none;' : '' ?>">
        <h2>Export</h2>

        <div>
            Press ctrl-space, or type a '<' character to activate autocompletion
        </div>

        <?php foreach ($settings->mappings as $class => $mappingCode) { ?>
            <div class="preview-block">
                <h2 class="class-name"><?php echo $class; ?></h2>
                <h3 class="file-name 
                <?php echo isset($settings->fileNameCssClasses[$class]) ? $settings->fileNameCssClasses[$class] : ''; ?>
                    ">
                        <?php echo isset($settings->fileNameMessages[$class]) ? $settings->fileNameMessages[$class] : ''; ?>
                        <?php echo isset($settings->fileNames[$class]) ? $settings->fileNames[$class] : ''; ?>
                </h3>

                <textarea
                    name="mappings[<?php echo $class; ?>]" 
                    <?php // echo 'rows="' . (3 + substr_count($mappingCode, "\n")) . '"'; ?>
                    data-format="<?php echo $settings->format; ?>"><?php echo $mappingCode; ?></textarea>

            </div>

        <?php } ?>

        <div class="form-group">
            <button class="btn btn-reset btn-default btn-block" name="action" value="update">Update Mappings</button>
            <button class="btn btn-submit btn-primary btn-block should-confirm" name="action" value="generate">Generate Mapping Files</button>
        </div>
    </section>
</form>
