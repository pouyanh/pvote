<?php

namespace PVote\Controller;


use PVote\Base\Controller;

class VoteController extends Controller
{
    public function commitAction()
    {
        if ($this->request->isPost()) {
            $input = $this->request->getPost('vote');

            $address = $_SERVER['REMOTE_ADDR'];

            $votesFilename = $this->di->getConfig()->get('polling.results.filename');
            $votesDoc = new \DOMDocument();
            if (is_readable($votesFilename)) {
                $votesDoc->load($votesFilename);
            } else {
                $votesDoc->loadXML('<votes/>');
            }
            $votesXpath = new \DOMXPath($votesDoc);
            $voteQ = $votesXpath->query(sprintf('/votes/vote[@address = "%s"]', $address));

            if (0 === $voteQ->length) {
                $optionsDoc = new \DOMDocument();
                $optionsDoc->load($this->di->getConfig()->get('polling.options.filename'));
                $optionsXpath = new \DOMXPath($optionsDoc);

                if ($optionsXpath->query(sprintf('/options/option[@id = "%s"]', $input))->length > 0) {
                    $vote = $votesDoc->createElement('vote');
                    $vote->setAttribute('address', $address);
                    $vote->setAttribute('vote', $input);
                    $votesDoc->documentElement->appendChild($vote);
                    $votesDoc->save($votesFilename);
                }
            }

            return $this->dispatcher->forward(
                [
                    'controller' => 'vote',
                    'action' => 'results'
                ]
            );
        }
    }

    public function resultsAction()
    {
        $votesFilename = $this->di->getConfig()->get('polling.results.filename');
        $votesDoc = new \DOMDocument();
        if (is_readable($votesFilename)) {
            $votesDoc->load($votesFilename);
        } else {
            return $this->dispatcher->forward(
                [
                    'controller' => 'index',
                    'action' => 'index'
                ]
            );
        }

        $optionsDoc = new \DOMDocument();
        $optionsDoc->load($this->di->getConfig()->get('polling.options.filename'));
        $optionsXpath = new \DOMXPath($optionsDoc);

        $options = [];

        foreach ($optionsXpath->query('/options/option') as $option) {
            $options[$option->getAttribute('id')] = [
                'count' => 0,
                'title' => $optionsXpath->query('./title', $option)->item(0)->textContent
            ];
        }

        $votesXpath = new \DOMXPath($votesDoc);
        $votesQ = $votesXpath->query('/votes/vote');

        $totalCount = 0;
        foreach ($votesQ as $vote) {
            if (isset($options[$vote->getAttribute('vote')])) {
                $options[$vote->getAttribute('vote')]['count']++;
                $totalCount++;
            }
        }

        $this->view->setVar('options', $options);
        $this->view->setVar('count', $totalCount);
    }
}
