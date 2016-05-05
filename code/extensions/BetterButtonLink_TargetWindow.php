<?php

/**
 * Class BetterButtonLink_TargetWindow
 */

class BetterButtonLink_TargetWindow extends BetterButtonLink {

    /**
     * Gets the HTML representing the button
     * @return string
     */
    public function getButtonHTML() {
        return sprintf(
            '<a class="ss-ui-button %s" href="%s" target="_blank">%s</a>',
            $this->extraClass(),
            $this->getButtonLink(),
            $this->getButtonText()
        );
    }

}