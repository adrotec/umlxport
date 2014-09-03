$(function() {
    
    var entity = {
        attrs: {
            name: null,
            table: null,
            schema: null,
            'repository-class': null,
            'inheritance-type': ['SINGLE_TABLE', 'JOINED', 'TABLE_PER_CLASS'],
            'change-tracking-policy': ['DEFERRED_IMPLICIT', 'DEFERRED_EXPLICIT', 'NOTIFY'],
            'read-only': ['true', 'false']
        },
        children: ['field', 'one-to-one', 'one-to-many', 'many-to-one']
    }
    var tags = {
        "!top": ["doctrine-mapping"],
        "!attrs": {
        },
        'doctrine-mapping': {
            attrs: {
                xmlns: ['http://doctrine-project.org/schemas/orm/doctrine-mapping'],
                'xmlns:xsi': ['http://www.w3.org/2001/XMLSchema-instance'],
                'xsi:schemaLocation': ['http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd']
            },
            children: ['entity', 'mapped-superclass', 'embeddable']
        },
        entity: entity,
        'mapped-superclass': entity,
        embeddable: entity,
        field: {
            attrs: {
                name: null,
                type: ['string', 'integer', 'float', 'text', 'boolean', 'date', 'datetime'],
                column: null,
                length: null,
                unique: ['true', 'false'],
                nullable: ['true', 'false'],
            }
        },
        'many-to-one': {
            attrs: {
                'target-entity': null,
                field: null,
                'inversed-by': null,
            },
        },
        'one-to-one': {
            attrs: {
                field: null,
                'target-entity': null,
                'mapped-by': null,
                'inversed-by': null,
            },
        },
        'one-to-many': {
            attrs: {
                'target-entity': null,
                'mapped-by': null,
                field: null,
            },
        },
    };

    function completeAfter(cm, pred) {
        var cur = cm.getCursor();
        if (!pred || pred())
            setTimeout(function() {
                if (!cm.state.completionActive)
                    cm.showHint({completeSingle: false});
            }, 100);
        return CodeMirror.Pass;
    }

    function completeIfAfterLt(cm) {
        return completeAfter(cm, function() {
            var cur = cm.getCursor();
            return cm.getRange(CodeMirror.Pos(cur.line, cur.ch - 1), cur) == "<";
        });
    }

    function completeIfInTag(cm) {
        return completeAfter(cm, function() {
            var tok = cm.getTokenAt(cm.getCursor());
            if (tok.type == "string" && (!/['"]/.test(tok.string.charAt(tok.string.length - 1)) || tok.string.length == 1))
                return false;
            var inner = CodeMirror.innerMode(cm.getMode(), tok.state).state;
            return inner.tagName;
        });
    }

    $('.preview-block textarea').each(function(i, textarea) {
//        return;
        var $textarea = $(textarea);
        var options = {
            lineNumbers: true,
            viewportMargin: Infinity,
            extraKeys: {},
            hintOptions: {},
            matchBrackets: true,
        };
        var format = $textarea.attr('data-format');
        if (format) {
            if (format == 'yml') {
                options.mode = 'yaml';
            }
            else if (format == 'annotation' || format == 'php') {
                options.mode = 'application/x-httpd-php';
            }
            else {
                options.mode = format;
            }
        }
        if(options.mode == 'xml'){
            options.extraKeys = {
                "'<'": completeAfter,
                "'/'": completeIfAfterLt,
                "' '": completeIfInTag,
                "'='": completeIfInTag,
                "Ctrl-Space": "autocomplete"
            };
            options.hintOptions.schemaInfo = tags; 
        }
        var editor = CodeMirror.fromTextArea(textarea, options);
    });

});