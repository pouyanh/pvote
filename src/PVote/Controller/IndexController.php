<?php
/**
 * Created by IntelliJ IDEA.
 * User: pouyan
 * Date: 8/25/14
 * Time: 3:58 AM
 */

namespace PVote\Controller;

use PVote\Base\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
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

        if ($voteQ->length > 0) {
            return $this->dispatcher->forward(
                [
                    'controller' => 'vote',
                    'action' => 'results'
                ]
            );
        }

        $optionsDoc = new \DOMDocument();
        $optionsDoc->load($this->di->getConfig()->get('polling.options.filename'));
        $optionsXpath = new \DOMXPath($optionsDoc);

        $options = [];

        foreach ($optionsXpath->query('/options/option') as $option) {
            $options[$option->getAttribute('id')] = $optionsXpath->query(
                './title',
                $option
            )->item(0)->textContent;
        }

        $this->view->setVar('options', $options);
    }
}
