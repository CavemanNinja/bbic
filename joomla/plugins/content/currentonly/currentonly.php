<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//jimport('joomla.plugin.plugin');

class plgContentCurrentOnly extends JPlugin
{
        /**
         * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
         * If you want to support 3.0 series you must override the constructor
         *
         * @var    boolean
         * @since  3.1
         */
        protected $autoloadLanguage = true;
 
        // function onContentPrepare($context, &$article, &$params, $limitstart)
        // {
        //         //add your plugin codes here
        //         //no return value
        //         var_dump($context);
        //         var_dump($params);
        //         var_dump($article);

        // }

        /**
         * Plugin method with the same name as the event will be called automatically.
         */
         function onContentBeforeDisplay($context, &$article, &$params, $limit=0)
         {
                /*
                 * Plugin code goes here.
                 * You can access database and application objects and parameters via $this->db,
                 * $this->app and $this->params respectively
                */

                $user = JFactory::getUser();
                //$name = JFactory::getUser()->name;
                //$author = $article->author;
                //$params->set("logged_in", $name);
                // $article->logged_in = $name;


                echo "\n------------------USER-------------------\n";
                var_dump($user);
                echo "\n------------------CONTEXT-------------------\n";
                var_dump($context);
                echo "\n------------------ARTICLE-------------------\n";
                var_dump($article);
                echo "\n------------------PARAMS-------------------\n";
                var_dump($params);
            
                return "";
        }
}
?>
