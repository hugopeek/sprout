<?php
abstract class SproutBaseManagerController extends modExtraManagerController {
    /** @var \Sprout\Sprout $sprout */
    public $sprout;

    public function initialize(): void
    {
        $this->sprout = $this->modx->services->get('sprout');

        $this->addCss($this->sprout->getOption('cssUrl') . 'mgr.css');
        $this->addJavascript($this->sprout->getOption('jsUrl') . 'mgr/sprout.js');

        $this->addHtml('
            <script type="text/javascript">
                Ext.onReady(function() {
                    sprout.config = '.$this->modx->toJSON($this->sprout->config).';
                });
            </script>
        ');

        parent::initialize();
    }

    public function getLanguageTopics(): array
    {
        return array('sprout:default');
    }

    public function checkPermissions(): bool
    {
        return true;
    }
}
