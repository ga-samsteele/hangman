<?php

namespace SamSteele\Hangman\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \SamSteele\Hangman\Model\Game\State as GameState;

class Canvas extends Template
{
    protected $gameState;

    public function __construct(
        Context $context,
        GameState $gameState
    ) {
        parent::__construct($context);

        $this->gameState = $gameState;
    }

    public function getImageByGameState(int $state) : string
    {
        /* Fetch ASCII representation of current game state from model constants */
        return $this->gameState->getImageByState($state);
    }

    public function getGameState() : int
    {
        return $this->gameState->getCurrentState();
    }
}
