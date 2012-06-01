<?php
/**
 * Won - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/**
 * SkinTemplate class for Won skin
 * @ingroup Skins
 */
class SkinWon extends SkinTemplate {

	var $skinname = 'won', $stylename = 'won',
		$template = 'WonTemplate', $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath, $wgRequest;

		parent::initPage( $out );

		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS fille since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $wgRequest->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
		);

		$out->addModuleScripts( 'skins.won' );
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ){
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'skins.won' );
		$out->addStyle( 'won/screen.css', 'screen' );
		$out->addStyle( 'won/occupy.css', 'screen' );
	}
}

/**
 * QuickTemplate class for Vector skin
 * @ingroup Skins
 */
class WonTemplate extends BaseTemplate {

	/* Members */

	/**
	 * @var Skin Cached skin object
	 */
	var $skin;

	/* Functions */

	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgLang, $wgVectorUseIconWatch;

		$this->skin = $this->data['skin'];

		// Build additional attributes for navigation urls
		//$nav = $this->skin->buildNavigationUrls();
		$nav = $this->data['content_navigation'];

		if ( $wgVectorUseIconWatch ) {
			$mode = $this->skin->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}
		// Output HTML Page
		$this->html( 'headelement' );
?>

<!-- <link rel="stylesheet" href="http://occupy.net/css/screen.css" type="text/css" media="screen, projection"> -->
<!-- <link rel="stylesheet" href="http://occupy.net/css/occupy_screen.css" type="text/css" media="screen, projection"> -->
<!-- <link rel="stylesheet" href="http://occupy.net/css/print.css" type="text/css" media="print"> -->

<script type="text/javascript" src="http://nav.occupy.net/occupynet_nav.js"></script>

<!-- content -->
<div id="content">
  <div id="wikititle">
    <div class="logo">
	<h1 id="subLogo"><a href="/">wiki.occupy.net</a></h1>
	<h5 id="tagline">A collection of information for activists worldwide</h5>
    </div>
  </div>


  <table id="maintable" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td style="vertical-align:top;">
	<!-- leftpanel -->
	<table id="leftpanel" cellspacing="0" cellpadding="0" border="0">
	  <tr>
	    <td id="wikinavigation">

		<div id="wm_navigation">
		<?php $this->renderNavigation( array( 'PERSONAL' ) ); ?>
		<?php $this->renderPortals( $this->data['sidebar'] ); ?>
		<?php $this->renderNavigation( array( 'TOOLBOX' ) ); ?>
		</div> <!-- /wm_navigation -->

	    </td>
	  </tr>
	</table> 
	<!-- /leftpanel -->

      </td>
      <td>
	<table id="main_content" cellspacing="0" cellpadding="0" border="0">
	  <tr>
	    <td>

		<!-- wikitoolbar -->
		<div id="wikitoolbar">

		  <!--SearchBox-->
		  <form method="get" id="searchform" action="<?php //$this->text('searchaction') ?>">
		    <input type="text"
                    <?php
                    if($this->haveMsg('accesskey-search')) {
                      ?>accesskey="<?php $this->msg('accesskey-search') ?>"<?php }
                    if( isset( $this->data['search'] ) ) {
                      ?> value="<?php $this->text('search') ?>"<?php } ?>
                    class="textbox" name="search" id="searchInput" /><input type='submit' id="searchsubmit" value="<?php

                    // Fix for MediaWiki prior to 1.8
                    if($GLOBALS['wgVersion'] >= '1.8')
                    {
                    $this->msg('searchbutton');
                    } else {
                    $this->msg('search');
                    }
                    ?>" />
		  </form> 
		  <!--/SearchBox -->

		  <!--mainmenu-->
		  <div id="wikimenuitems">
		    <?php $this->renderNavigation( array( 'VIEWS' ) ); ?>
		    <?php $this->renderNavigation( array( 'NAMESPACES' ) ); ?>
		    <?php $this->renderNavigation( array( 'ACTIONS' ) ); ?>
		  </div>


		</div> <!-- /wikitoolbar -->

	    </td>
	  </tr>
	  <tr>
	    <td>
	    	<h1 id="firstHeading" class="firstHeading"><?php $this->html( 'title' ) ?></h1>
	      <div id="bodytext"><?php $this->html( 'bodytext' ) ?></div>
	    </td>
	  </tr>
      <tr>
      	<td>
			<?php if ( $this->data['catlinks'] ): ?>
			<!-- catlinks -->
			<?php $this->html( 'catlinks' ); ?>
			<!-- /catlinks -->
			<?php endif; ?>
	    </td>
	  </tr>
      <tr>
      	<td>
	      <div id="socialmedia">&nbsp;
	      </div>
	    </td>
	  </tr>
	</table>
      </td>
    </tr>
  </table>
</div> <!-- /content -->
			
<div style="clear:both;"></div>

<div id="wikifooter"<?php $this->html( 'userlangattributes' ) ?>>

	<?php foreach( $this->getFooterLinks() as $category => $links ): ?>
	<ul id="footer-<?php echo $category ?>">
		<?php foreach( $links as $link ): ?>
		<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
        <?php endforeach; ?>
	</ul>
	<?php endforeach; ?>
	<?php $footericons = $this->getFooterIcons("icononly");
		if ( count( $footericons ) > 0 ): ?>
			<ul id="footer-icons" class="noprint">
			<?php foreach ( $footericons as $blockName => $footerIcons ): ?>
                        <li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
				<?php foreach ( $footerIcons as $icon ): ?>
					<?php echo $this->skin->makeFooterIcon( $icon ); ?>
				<?php endforeach; ?>
			</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<div style="clear:both"></div>
</div> <!-- /footer -->
<div id="occupyfooter">
	<div id="brought">
	Brought to you by OCCUPY TECH
	</div>
</div>
<?php $this->printTrail(); ?>
	</body>
</html>
<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	private function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false )
				continue;

			echo "\n<!-- {$name} -->\n";
			switch( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					//if ( $this->data['language_urls'] ) {
					//	$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					//}
					break;
				case 'navigation':
					$this->renderPortal( $name, $content);
					break;
				case 'PERSONAL':
					$this->renderPortal( $name, $content);
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}

	private function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( !isset( $msg ) ) {
			$msg = $name;
		}
		?>
<div class="portal" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
        <div class="body">
<?php
		if ( is_array( $content ) ): ?>
		<ul<?php if( 'tb' == $name || 'PERSONAL' == $name ){ ?> class="mw-collapsible mw-collapsed"<?php } ?>>
		<?php if( 'tb' == $name || 'PERSONAL' == $name ){ $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); }?>


<?php
			foreach( $content as $key => $val ): ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			endforeach;
			if ( isset( $hook ) ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
		</ul>
<?php
		else: ?>
		<?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
<?php
		endif; ?>
	</div>
</div>
<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 */
	private function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser, $wgLang;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $wgLang->isRTL() ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			echo "\n<!-- {$name} -->\n";
			switch ( $element ) {
				case 'NAMESPACES':
?>
<div id="p-namespaces" class="vectorTabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<!-- <h5><?php //$this->msg( 'namespaces' ) ?></h5> -->
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'VARIANTS':
?>
<div id="p-variants" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<?php if ( $wgVectorShowVariantName ): ?>
		<h4>
		<?php foreach ( $this->data['variant_urls'] as $link ): ?>
			<?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
				<?php echo htmlspecialchars( $link['text'] ) ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</h4>
	<?php endif; ?>
	<h5><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['variant_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'VIEWS':
?>
<div id="p-views" class="vectorTabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { } ?>">
	<!-- <h5><?php //$this->msg('views') ?></h5> -->
	<ul<?php $this->html('userlangattributes') ?>>
		<?php foreach ( $this->data['view_urls'] as $link ): ?>
			<li<?php echo $link['attributes'] ?>>     <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a>     </li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'ACTIONS':
?>
<div id="p-cactions" class="vectorMenu<?php if ( count( $this->data['action_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<!-- <h5><span><?php //$this->msg( 'actions' ) ?></span><a href="#"></a></h5> -->
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['action_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'PERSONAL':
?>
<div id="p-personal" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<ul class="mw-collapsible mw-collapsed" <?php $this->html( 'userlangattributes' ) ?>>
		Profile
<?php			foreach( $this->getPersonalTools() as $key => $item ) { ?>
		<?php echo $this->makeListItem( $key, $item ); ?>

<?php			} ?>
	</ul>
</div>
<?php
				break;
				case 'SEARCH':
?>
<div id="p-search">
	<h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		<?php if ( $wgVectorUseSimpleSearch && $wgUser->getOption( 'vector-simplesearch' ) ): ?>
		<div id="simpleSearch">
			<?php if ( $this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-rtl.png' ) ) ); ?>
			<?php endif; ?>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'text' ) ); ?>
			<?php if ( !$this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-ltr.png' ) ) ); ?>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
		<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ); ?>
		<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) ); ?>
		<?php endif; ?>
	</form>
</div>
<?php

				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}
}
