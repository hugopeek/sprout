<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';

class SproutManageManagerController extends SproutBaseManagerController
{

    public function process(array $scriptProperties = []): void
    {
    }

    public function getPageTitle(): string
    {
        return $this->modx->lexicon('sprout');
    }

    public function loadCustomCssJs(): void
    {
        $this->addLastJavascript($this->sprout->getOption('jsUrl') . 'mgr/widgets/manage.panel.js');
        $this->addLastJavascript($this->sprout->getOption('jsUrl') . 'mgr/sections/manage.js');

        $this->addHtml(
            '
            <script type="text/javascript">
                Ext.onReady(function() {
                    MODx.load({ xtype: "sprout-page-manage"});
                });
            </script>
        '
        );
    }

    public function getTemplateFile(): string
    {
        return $this->sprout->getOption('templatesPath') . 'manage.tpl';
    }

}
