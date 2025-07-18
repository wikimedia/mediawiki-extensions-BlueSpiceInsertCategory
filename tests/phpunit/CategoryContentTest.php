<?php

namespace BlueSpice\InsertCategory\Tests;

use BlueSpice\InsertCategory\Content\CategoryContent;
use InvalidArgumentException;
use MediaWiki\Title\Title;
use PHPUnit\Framework\TestCase;

class CategoryContentTest extends TestCase {
	/**
	 * @covers \BlueSpice\InsertCategory\CategoryManipulator\WikitextCategoryManipulator::getCategories
	 * @covers \BlueSpice\InsertCategory\CategoryManipulator\WikitextCategoryManipulator::setCategories
	 */
	public function testCategoryOperation() {
		$content = new CategoryContent();

		$this->assertSame( [], $content->getCategories() );

		$content->setCategories( [
			$this->mockCategoryTitle( 'Foo' ),
			$this->mockCategoryTitle( 'Bar' ),
		] );
		$this->assertSame(
			[ 'Foo', 'Bar' ],
			$content->getCategories()
		);

		$content->addCategory( $this->mockCategoryTitle( 'Baz' ) );
		$this->assertSame(
			[ 'Foo', 'Bar', 'Baz' ],
			$content->getCategories()
		);

		$content->removeCategory( $this->mockCategoryTitle( 'Bar' ) );
		$this->assertSame(
			[ 'Foo', 'Baz' ],
			$content->getCategories()
		);

		$content->setCategories( [
			$this->mockCategoryTitle( 'AnotherCategory' ),
		] );
		$this->assertSame(
			[ 'AnotherCategory' ],
			$content->getCategories()
		);

		$this->expectException( InvalidArgumentException::class );
		$content->setCategories( [
			$this->mockNonCategoryTitle( 'NotACategory' ),
		] );
	}

	/**
	 * @param string $name
	 * @return Title
	 */
	private function mockCategoryTitle( string $name ) {
		$mock = $this->createMock( Title::class );
		$mock->method( 'getText' )->willReturn( $name );
		$mock->method( 'getNamespace' )->willReturn( NS_CATEGORY );
		return $mock;
	}

	/**
	 * @param string $name
	 * @return Title
	 */
	private function mockNonCategoryTitle( string $name ) {
		$mock = $this->createMock( Title::class );
		$mock->method( 'getText' )->willReturn( $name );
		$mock->method( 'getNamespace' )->willReturn( NS_MAIN );
		return $mock;
	}
}
