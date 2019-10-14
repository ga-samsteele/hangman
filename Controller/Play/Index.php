<?php

namespace SamSteele\Hangman\Controller\Play;

use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Action\Action;

class Index extends Action
{
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        /* Just build the page, should probably check if a game needs initializing here instead as a todo */
        return $this->pageFactory->create();
    }
}
