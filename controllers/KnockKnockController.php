<?php
/**
 * KnockKnock plugin for Craft CMS
 *
 * KnockKnock Controller
 *
 * --snip--
 * Generally speaking, controllers are the middlemen between the front end of the CP/website and your plugin’s
 * services. They contain action methods which handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering post data, saving it on a model,
 * passing the model off to a service, and then responding to the request appropriately depending on the service
 * method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what the method does (for example,
 * actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 * --snip--
 *
 * @author    Derrick Grigg
 * @copyright Copyright (c) 2017 Derrick Grigg
 * @link      dgrigg.com
 * @package   KnockKnock
 * @since     1.0.0
 */

namespace Craft;

class KnockKnockController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array('actionAsk', 'actionAnswer');

    public function actionAsk()
    {
        craft()->templates->setTemplateMode(TemplateMode::CP);
        $data['redir'] = craft()->userSession->getFlash('redir');

        if ($data['redir'] == ''){
            $data['redir'] = '/';
        }

        $this->renderTemplate('knockknock/ask', $data);
    }

    public function actionAnswer()
    {
        $password = craft()->request->getPost('password');
        $accessPassword = craft()->plugins->getPlugin('knockKnock')->getSettings()->password;

        if ($accessPassword == $password)
        {
            $cookie = new HttpCookie('siteAccessToken', craft()->request->csrfToken, [ 'expire' => time() + 3600 ]);
            craft()->request->getCookies()->add( $cookie->name, $cookie );
            craft()->request->redirect(craft()->request->getParam('redir'));
        } else {
            $data['redir'] = craft()->request->getParam('redir');
            $data['error'] = 'Invalid password';
            craft()->templates->setTemplateMode(TemplateMode::CP);
            $this->renderTemplate('knockknock/ask', $data);

        }
    }
}