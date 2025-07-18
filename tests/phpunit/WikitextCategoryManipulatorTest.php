<?php

namespace BlueSpice\InsertCategory\Tests;

use BlueSpice\InsertCategory\CategoryManipulator\WikitextCategoryManipulator;
use MediaWiki\Content\WikitextContent;
use MediaWiki\Content\WikitextContentHandler;
use MediaWiki\Page\PageIdentity;
use MediaWiki\Page\WikiPageFactory;
use MediaWiki\Permissions\Authority;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Storage\PageUpdater;
use MediaWiki\Storage\PageUpdateStatus;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use PHPUnit\Framework\TestCase;

class WikitextCategoryManipulatorTest extends TestCase {
	/**
	 * @covers \BlueSpice\InsertCategory\CategoryManipulator\WikitextCategoryManipulator::getCategories
	 * @covers \BlueSpice\InsertCategory\CategoryManipulator\WikitextCategoryManipulator::setCategories
	 */
	public function testGetSetCategories() {
		$text = <<<WIKITEXT
Dummy

[[Help:ABC]]
[[Category:ABC|1]]
[[Category:ABC]]
[[Category:Bar dummy]]
[[Kategorie:Foo]]
WIKITEXT;

		$modifiedText = <<<WIKITEXT
Dummy

[[Help:ABC]]
[[Kategorie:Foo]]
[[Category:NewCategory]]
WIKITEXT;

		$contentHandler = $this->createMock( WikitextContentHandler::class );
		$contentHandler->method( 'unserializeContent' )->willReturnCallback(
			static function ( $text ) {
				return new WikitextContent( $text );
			} );
		$content = $this->createMock( WikitextContent::class );
		$content->method( 'getText' )->willReturn( $text );
		$content->method( 'getContentHandler' )->willReturn( $contentHandler );

		$revision = $this->createMock( RevisionRecord::class );
		$revision->method( 'getContent' )->willReturn( $content );

		$revisionLookup = $this->createMock( RevisionLookup::class );
		$revisionLookup->method( 'getRevisionByTitle' )->willReturn( $revision );

		$titleFactory = $this->createMock( TitleFactory::class );
		$titleFactory->method( 'newFromText' )->willReturnCallback(
			function ( $text ) {
				$namespace = explode( ':', $text, 2 )[0];
				$title = $this->createMock( Title::class );
				$title->method( 'getText' )->willReturn( substr( $text, strpos( $text, ':' ) + 1 ) );
				$title->method( 'getNamespace' )->willReturn(
					$namespace === 'Category' || $namespace === 'Kategorie' ? NS_CATEGORY : NS_MAIN
				);
				$title->method( 'getPrefixedText' )->willReturn( $text );
				return $title;
			} );

		$updater = $this->createMock( PageUpdater::class );
		$updater->method( 'getStatus' )->willReturn( PageUpdateStatus::newGood() );
		$updater->expects( $this->once() )->method( 'setContent' )->with(
			SlotRecord::MAIN, new WikitextContent( $modifiedText )
		);

		$wikiPage = $this->createMock( \WikiPage::class );
		$wikiPage->method( 'newPageUpdater' )->willReturn( $updater );
		$wikiPageFactory = $this->createMock( WikiPageFactory::class );
		$wikiPageFactory->method( 'newFromTitle' )->willReturn( $wikiPage );

		$manipulator = new WikitextCategoryManipulator(
			$wikiPageFactory,
			$revisionLookup,
			$titleFactory
		);

		$this->assertSame(
			[
				'ABC' => [ '[[Category:ABC|1]]', '[[Category:ABC]]' ],
				'Bar dummy' => [ '[[Category:Bar dummy]]' ],
				'Foo' => [ '[[Kategorie:Foo]]' ]
			],
			$manipulator->getCategories( $this->createMock( PageIdentity::class ) )
		);

		$manipulator->setCategories(
			$this->createMock( PageIdentity::class ),
			[
				$titleFactory->newFromText( 'Category:NewCategory' ),
				$titleFactory->newFromText( 'Category:Foo' )
			],
			$this->createMock( Authority::class )
		);
	}
}
