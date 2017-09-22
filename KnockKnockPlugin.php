<?php
/**
 * KnockKnock plugin for Craft CMS
 *
 * Password protect your website with a single password. No need to use Apache or Nginx
 * This only protects urls served up through Craft, it does not protect static files (ie js, css, images)
 *
 * @author    Derrick Grigg
 * @copyright Copyright (c) 2017 Derrick Grigg
 * @link      https://dgrigg.com
 * @package   KnockKnock
 * @since     1.0.0
 */

namespace Craft;

class KnockKnockPlugin extends BasePlugin
{

    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Knock Knock');
    }

    /**
     * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
     * on the primary plugin class:
     *
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Password protect your public facing Craft website with a single password');
    }

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * As of Craft 2.5, Craft no longer takes the whole site down every time a plugin’s version number changes, in
     * case there are any new migrations that need to be run. Instead plugins must explicitly tell Craft that they
     * have new migrations by returning a new (higher) schema version number with a getSchemaVersion() method on
     * their primary plugin class:
     *
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * Returns the developer’s name.
     *
     * @return string
     */
    public function getDeveloper()
    {
        return 'Derrick Grigg';
    }

    /**
     * Returns the developer’s website URL.
     *
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://dgrigg.com';
    }

    /**
     * Returns whether the plugin should get its own tab in the CP header.
     *
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }

    /**
     * Defines the attributes that model your plugin’s available settings.
     *
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'password' => array(AttributeType::String, 'label' => 'Password', 'default' => '', 'required' => true, 'minLength' => 8),
        );
    }

    /**
     * set friendly urls to be used by plugin
     */

    public function registerSiteRoutes()
    {
        return array(
            'knockKnock/whoIsThere' => array('action' => 'knockKnock/ask')
        );
    }

    /**
     * Returns the HTML that displays your plugin’s settings.
     *
     * @return mixed
     */
    public function getSettingsHtml()
    {
       return craft()->templates->render('knockknock/settings', array(
           'settings' => $this->getSettings()
       ));
    }


    public function init()
    {
        parent::init();
        $url = craft()->request->getUrl();
        $token = craft()->request->getCookie('siteAccessToken');
        $user = craft()->userSession->getUser();

        //force challenge for non authenticated site visitors
        if ((craft()->request->isSiteRequest()) && (!$user) && ($token == '') && (stripos($url, 'knockknock') === FALSE) ) {
            craft()->userSession->setFlash( 'redir', $url);
            $redir = '/knockKnock/whoIsThere';
            craft()->request->redirect($redir);
        }
    }



}