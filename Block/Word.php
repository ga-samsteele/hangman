<?php

namespace SamSteele\Hangman\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \SamSteele\Hangman\Model\Game\State as GameState;

class Word extends Template
{
    protected $gameState;

    public function __construct(
        Context $context,
        GameState $gameState
    ) {
        parent::__construct($context);

        $this->gameState = $gameState;
    }

    protected function getCurrentWord() : string
    {
        return $this->gameState->getCurrentWord();
    }

    public function getMaskedWord() : string
    {
        /* If there are already guessed letters */
        if (count($this->gameState->getGuessedLetters())) {
            /* Output the guessed ones, mask the ones still not guessed */
            return preg_replace(
                "/[^". implode(',', $this->gameState->getGuessedLetters()) ."]/", 
                "_", 
                $this->getCurrentWord()
            );
        } else {
            /* Else just mask the whole string */
            return str_repeat('_', strlen($this->getCurrentWord()));
        }
    }

}
