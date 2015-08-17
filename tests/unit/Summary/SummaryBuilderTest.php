<?php

namespace Wikibase\DataModel\Services\Tests;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Services\Summary\SummaryBuilder;

class SummaryBuilderTest extends \PHPUnit_Framework_TestCase {

	public function provideStuff() {
		$emptyItem = new Item();

		$itemWithOneLabelEn = new Item();
		$itemWithOneLabelEn->setLabel( 'en', 'Foo' );

		$itemWithOneDescriptionEnFoo = new Item();
		$itemWithOneDescriptionEnFoo->setDescription( 'en', 'Foo' );

		$itemWithOneDescriptionEnBar = new Item();
		$itemWithOneDescriptionEnBar->setDescription( 'en', 'Bar' );

		$itemWithOneDescriptionDe = new Item();
		$itemWithOneDescriptionDe->setDescription( 'de', 'Foo' );

		return array(
			array(
				$emptyItem,
				$emptyItem,
				'Null change'
			),
			array(
				$itemWithOneDescriptionEnFoo,
				$itemWithOneDescriptionEnFoo,
				'Null change'
			),
			array(
				$itemWithOneDescriptionEnFoo,
				$emptyItem,
				'Entity Emptied'
			),
			array(
				$emptyItem,
				$itemWithOneDescriptionEnFoo,
				'added 1 descriptions'
			),
			array(
				$itemWithOneDescriptionEnFoo,
				$itemWithOneDescriptionEnBar,
				'changed 1 descriptions'
			),
			array(
				$itemWithOneDescriptionEnFoo,
				$itemWithOneDescriptionDe,
				'added 1 descriptions, removed 1 descriptions'
			),
		);
	}

	/**
	 * @dataProvider provideStuff
	 */
	public function testGetPropertyIds( EntityDocument $from, EntityDocument $to, $expectedSummary ) {
		$builder = new SummaryBuilder();
		$summary = $builder->get( $from, $to );
		$this->assertEquals( $expectedSummary, $summary );
	}

}
