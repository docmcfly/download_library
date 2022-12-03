<?php
namespace Cylancer\DownloadLibrary\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use function GuzzleHttp\Promise\exception_for;

/**
 * *
 *
 * This file is part of the "Participants" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2020 C. Gogolin <service@cylancer.net>
 * C. Gogolin <service@cylancer.net>
 *
 * *
 */
/**
 * The repository for Commitments
 */
class EmailUtility implements SingletonInterface
{

    /**
     *
     * @param array $recipient
     *            recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender
     *            sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject
     *            subject of the email
     * @param string $templateName
     *            template name (UpperCamelCase)
     * @param array $variables
     *            variables to be passed to the Fluid view
     * @return boolean TRUE on success, otherwise false
     */
    public function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName, $extensionName, array $variables = array())
    {
//          debug($recipient);
//          debug($sender);
//          debug($variables);
        
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\Extbase\\Object\\ObjectManager');
        $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $emailView->getRequest()->setControllerExtensionName($extensionName);

        $viewDefinitions = $extbaseFrameworkConfiguration['plugin.']['tx_' . strtolower($extensionName) . '.']['view.'];
        
        $emailView->setTemplateRootPaths($viewDefinitions['templateRootPaths.']);
        $emailView->setLayoutRootPaths($viewDefinitions['layoutRootPaths.']);
        $emailView->setPartialRootPaths($viewDefinitions['partialRootPaths.']);

        $emailView->setTemplate($templateName);
        $emailView->assignMultiple($variables);
        
        // if you want to use german or other UTF-8 chars in subject enable next line
        $subject = $subject == null ? 'no subject' : $subject;
        $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
        // debug($recipient);
        // debug($sender);

        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = $objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject);

        // Possible attachments here
        // foreach ($attachments as $attachment) {
        // $message->attach(\Swift_Attachment::fromPath($attachment));
        // }

        $emailBodyHtml = $emailView->render();

        $emailView->setFormat('txt');
        $emailBodyTxt = $emailView->render();
        // transform <a> to a simple url.
        $emailBodyTxt = preg_replace('&<a.*href="(.+)".*/a>&', '$1', $emailBodyTxt);
        $emailBodyTxt = strip_tags( htmlspecialchars_decode($emailBodyTxt));
        $emailBodyTxt = str_replace('&hellip;', '…', $emailBodyTxt);
        
        
        $message->text($emailBodyTxt);

        // transform new lines to div tags.
        $emailBodyHtml = nl2br($emailBodyHtml);
        // $emailBodyHtml = str_replace("\n", '</div><div>', $emailBodyHtml);
        // $emailBodyHtml = preg_replace("&<div>\\s*</div>&", '<div><br/></div>', $emailBodyHtml);
//         debug($emailBodyHtml);
        // throw new \Exception();
        
        $message->html($emailBodyHtml);
        $message->send();
//         debug($emailBodyHtml); 
        // return true; 
        return $message->isSent();
    }
}
