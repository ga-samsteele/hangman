<?php

namespace SamSteele\Hangman\Model\Game;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Framework\Message\ManagerInterface as MessageManager;

class State extends AbstractModel
{
    /* Bunch of ASCII representations of the hangman game states */
    const GAME_STATES = [
        0 => '+------+
 |   |
     |
     |
     |
     |
=========',
        1 => '+------+
 |   |
 0   |
     |
     |
     |
=========',
        2 => '+------+
 |   |
 0   |
 |   |
     |
     |
=========',
        3 => '+------+
 |   |
 0   |
/|   |
     |
     |
=========',
        4 => '+------+
 |   |
 0   |
/|\  |
     |
     |
=========',
        5 => '+------+
 |   |
 0   |
/|\  |
/    |
     |
=========',
        6 => '+------+
 |   |
 0   |
/|\  |
/ \  |
     |
========='
    ];

    const DEFAULT_STATE = 0; /* Start point */
    const FINAL_STATE = 6; /* Game over point */

    protected $customerSession;
    protected $messageManager;

    public function __construct(
        CustomerSession $customerSession,
        MessageManager $messageManager
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
    }

    public function getDefaultState() : int
    {
        return static::DEFAULT_STATE;
    }

    public function getFinalState() : int
    {
        return static::FINAL_STATE;
    }

    public function getImageByState(int $state) : string
    {
        return static::GAME_STATES[$state];
    }

    public function getCurrentState() : int
    {
        /* Set game state to default (0) if no state preset, could move to action predispatch? */
        if ($this->customerSession->getHangmanGameState() === null) {
            $this->initializeGame();
        }

        return $this->customerSession->getHangmanGameState();
    }

    public function getCurrentWord() : string
    {
        return $this->customerSession->getHangmanWord() ?? '';
    }

    public function getGuessedLetters() : array
    {
        return $this->customerSession->getGuessedLetters() ?? [];
    }

    public function initializeGame() : void
    {
        $this->customerSession->setHangmanGameState($this->getDefaultState());
        $this->customerSession->setHangmanWord($this->getRandomWord());
        $this->customerSession->setGuessedLetters([]);
    }

    public function guess(string $guess) : void
    {
        $this->validateGuess($guess);
        
        /* If guessed letter is in word */
        if (strpos($this->customerSession->getHangmanWord(), $guess) !== false) {
            /* Save the correctly guessed letter */
            $guessedLetters = $this->customerSession->getGuessedLetters();
            $guessedLetters[] = $guess;
            $this->customerSession->setGuessedLetters($guessedLetters);

            /* If all unique characters in the word have been guessed */
            if (count(array_filter($this->customerSession->getGuessedLetters())) === count(array_filter(str_split($this->customerSession->getHangmanWord(), 1)))) {
                /* Game over, success */
                $this->messageManager->addSuccess("You won");
                $this->initializeGame();
            }
        } else {
            /* If guessed letter is not in word, increment game state */
            $this->customerSession->setHangmanGameState(
                $this->customerSession->getHangmanGameState() + 1
            );

            /* If this incorrect guess puts us to the final state */
            if ($this->customerSession->getHangmanGameState() == $this->getFinalState()) {
                /* Game over, failure */
                $this->messageManager->addWarning("You lost");
                $this->initializeGame();
            }
        }
    }

    protected function validateGuess(string $guess) : void
    {
        /* If not alphabetic character, or is more than 1 character */
        if (!ctype_alpha($guess) || strlen($guess) !== 1){
            throw new \Exception('You can only guess a single letter at a time.');
        }
    }

    protected function getRandomWord()
    {
        /* The first result for "random word api" in Google, does the job :) */
        return strtoupper(json_decode(file_get_contents("https://random-word-api.herokuapp.com/word?key=KCD9PLGK&number=1"))[0]);
    }
}
