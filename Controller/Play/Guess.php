<?php

namespace SamSteele\Hangman\Controller\Play;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\Controller\ResultFactory;
use \SamSteele\Hangman\Model\Game\State as GameState;

class Guess extends Action
{
    protected $pageFactory;
    protected $resultFactory;
    protected $gameState;

    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        GameState $gameState
    ) {
        parent::__construct($context);

        $this->resultFactory = $resultFactory;
        $this->gameState = $gameState;
    }

    public function execute()
    {
        /* We already redirect to referrer, so mightaswell build that first */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        /* Guess always to uppercase as random word is also always to upper */
        $guess = strtoupper($this->_request->getParam('letter'));

        /* Failsafe against random direct route hits */
        if (!$guess) {
            $this->messageManager->addError("No guess made");
            return $resultRedirect;
        }

        /* Check the letter & nicely handle exceptions */
        try {
            $this->gameState->guess($guess);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect;
        }

        return $resultRedirect;
    }
}
