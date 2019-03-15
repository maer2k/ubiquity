<?php

namespace Ubiquity\views\engine;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\TwigTest;
use Twig\Loader\FilesystemLoader;
use Ubiquity\cache\CacheManager;
use Ubiquity\controllers\Router;
use Ubiquity\controllers\Startup;
use Ubiquity\core\Framework;
use Ubiquity\events\EventsManager;
use Ubiquity\events\ViewEvents;
use Ubiquity\exceptions\ThemesException;
use Ubiquity\translation\TranslatorManager;
use Ubiquity\utils\base\UFileSystem;
use Ubiquity\themes\ThemesManager;
use Ubiquity\assets\AssetsManager;

/**
 * Ubiquity Twig template engine.
 *
 * Ubiquity\views\engine$Twig
 * This class is part of Ubiquity
 *
 * @author jcheron <myaddressmail@gmail.com>
 * @version 1.0.8
 *
 */
class Twig extends TemplateEngine {
	private $twig;
	private $loader;

	public function __construct($options = array()) {
		$loader = new FilesystemLoader ( \ROOT . \DS . "views" . \DS );
		$loader->addPath ( implode ( \DS, [ Startup::getFrameworkDir (),"..","core","views" ] ) . \DS, "framework" );
		$this->loader = $loader;
<<<<<<< HEAD

=======
		
		if (isset ( $options ["cache"] ) && $options ["cache"] === true){
			$options ["cache"] = CacheManager::getCacheSubDirectory ( "views" );
		}
		if(isset($options["activeTheme"])){
			ThemesManager::setActiveThemeFromTwig($options["activeTheme"]);
			self::setTheme($options["activeTheme"],ThemesManager::THEMES_FOLDER);
			unset($options["activeTheme"]);
		}
>>>>>>> [update] activeTheme setting
		$this->twig = new Environment ( $loader, $options );
		if (isset ( $options ["cache"] ) && $options ["cache"] === true) {
			$options ["cache"] = CacheManager::getCacheSubDirectory ( "views" );
		}
		if (isset ( $options ["activeTheme"] )) {
			ThemesManager::setActiveThemeFromTwig ( $options ["activeTheme"] );
			self::setTheme ( $options ["activeTheme"], ThemesManager::THEMES_FOLDER );
			unset ( $options ["activeTheme"] );
		}

<<<<<<< HEAD
		$this->addFunction ( 'path', function ($name, $params = [], $absolute = false) {
			return Router::path ( $name, $params, $absolute );
		} );

		$this->addFunction ( 'url', function ($name, $params) {
			return Router::url ( $name, $params );
		} );

		$this->addFunction ( 'css_', function ($resource, $parameters = [], $absolute = false) {
			return AssetsManager::css_ ( $resource, $parameters, $absolute );
		}, true );

		$this->addFunction ( 'css', function ($resource, $parameters = [], $absolute = false) {
			return AssetsManager::css ( $resource, $parameters, $absolute );
		}, true );

		$this->addFunction ( 'js', function ($resource, $parameters = [], $absolute = false) {
			return AssetsManager::js ( $resource, $parameters, $absolute );
		}, true );

		$this->addFunction ( 'js_', function ($resource, $parameters = [], $absolute = false) {
			return AssetsManager::js_ ( $resource, $parameters, $absolute );
		}, true );

		$this->addFunction ( 't', function ($context, $id, array $parameters = array(), $domain = null, $locale = null) {
=======
		$this->addFunction( 'path', function ($name, $params = [], $absolute = false) {
			return Router::path ( $name, $params, $absolute );
		} );
		
		$this->addFunction( 'url', function ($name, $params) {
			return Router::url ( $name, $params );
		} );
		
		$this->addFunction( 'css_', function ($resource, $absolute=false) {
			return AssetsManager::css_( $resource, $absolute );
		} );
			
		$this->addFunction( 'js_', function ($resource, $absolute=false) {
			return AssetsManager::js_( $resource, $absolute );
		} );

		$this->addFunction( 't', function ($context, $id, array $parameters = array(), $domain = null, $locale = null) {
>>>>>>> [update] activeTheme setting
			$trans = TranslatorManager::trans ( $id, $parameters, $domain, $locale );
			return $this->twig->createTemplate ( $trans )->render ( $context );
		}, [ 'needs_context' => true ] );

		$test = new TwigTest ( 'instanceOf', function ($var, $class) {
			return $var instanceof $class;
		} );
		$this->twig->addTest ( $test );
		$this->twig->addGlobal ( "app", new Framework () );
	}
<<<<<<< HEAD

	protected function addFunction($name, $callback, $safe = false) {
		$options = ($safe) ? [ 'is_safe' => [ 'html' ] ] : [ ];
		$this->twig->addFunction ( new TwigFunction ( $name, $callback, $options ) );
=======
	
	protected function addFunction($name,$callback){
		$this->twig->addFunction ( new TwigFunction ( $name, $callback ) );
>>>>>>> [update] activeTheme setting
	}

	/*
	 * (non-PHPdoc)
	 * @see TemplateEngine::render()
	 */
	public function render($viewName, $pData, $asString) {
		$pData ["config"] = Startup::getConfig ();
		EventsManager::trigger ( ViewEvents::BEFORE_RENDER, $viewName, $pData );
		$render = $this->twig->render ( $viewName, $pData );
		EventsManager::trigger ( ViewEvents::AFTER_RENDER, $render, $viewName, $pData );
		if ($asString) {
			return $render;
		} else {
			echo $render;
		}
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\views\engine\TemplateEngine::getBlockNames()
	 */
	public function getBlockNames($templateName) {
		return $this->twig->load ( $templateName )->getBlockNames ();
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see \Ubiquity\views\engine\TemplateEngine::getCode()
	 */
	public function getCode($templateName) {
		return UFileSystem::load ( $this->twig->load ( $templateName )->getSourceContext ()->getPath () );
	}

	/**
	 * Adds a new path in a namespace
	 *
	 * @param string $path The path to add
	 * @param string $namespace The namespace to use
	 */
	public function addPath(string $path, string $namespace) {
		$this->loader->addPath ( $path, $namespace );
	}

	/**
	 * Defines the activeTheme.
	 * **activeTheme** namespace is @activeTheme
	 *
	 * @param string $theme
	 * @param string $themeFolder
	 * @throws ThemesException
	 */
	public function setTheme($theme, $themeFolder = ThemesManager::THEMES_FOLDER) {
		$path = \ROOT . \DS . 'views' . \DS . $themeFolder . \DS . $theme;
		if ($theme == '') {
			$path = \ROOT . \DS . 'views';
		}
		if (file_exists ( $path )) {
			$this->loader->setPaths ( [ $path ], "activeTheme" );
		} else {
			throw new ThemesException ( sprintf ( 'The path `%s` does not exists!', $path ) );
		}
	}
}