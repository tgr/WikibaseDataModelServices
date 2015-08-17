<?php

namespace Wikibase\DataModel\Services\Summary;

use Comparable;
use Diff\DiffOp\AtomicDiffOp;
use Diff\DiffOp\Diff\Diff;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Services\Diff\EntityDiffer;
use Wikibase\DataModel\Services\Diff\ItemDiff;

/**
 * This is a stupid rough draft.....@deprecated
 * RFC......
 *
 * TODO: i18n, plurals, linking stuff?, priortise certain changes, limit?
 */
class SummaryBuilder {

	/**
	 * @param EntityDocument $orig
	 * @param EntityDocument $changed
	 *
	 * @return string
	 */
	public function get( EntityDocument $orig, EntityDocument $changed ) {

		//TODO could just check the diff is null?
		if ( $orig instanceof Comparable && $changed instanceof Comparable ) {
			if ( $orig->equals( $changed ) ) {
				return 'Null change';
			}
		}

		//TODO isEmpty should be in an interface?
		if ( method_exists( $changed, 'isEmpty' ) ) {
			if ( $changed->isEmpty( $changed ) ) {
				return 'Entity Emptied';
			}
		}

		//TODO inject entityDiffer
		$entityDiffer = new EntityDiffer();
		$entityDiff = $entityDiffer->diffEntities( $orig, $changed );

		/** @var Diff[] $diffs */
		$diffs = array();
		$diffs['labels'] = $entityDiff->getLabelsDiff();
		$diffs['descriptions'] = $entityDiff->getDescriptionsDiff();
		$diffs['aliases'] = $entityDiff->getAliasesDiff();
		$diffs['claims'] = $entityDiff->getClaimsDiff();
		if( $entityDiff instanceof ItemDiff ) {
			$diffs['sitelinks'] = $entityDiff->getSiteLinkDiff();
		}

		foreach ( $diffs as $type => $diff ) {
			if( $diff->isEmpty() ) {
				unset( $diffs[$type] );
			}
		}

		$summaryParts = array();
		foreach( $diffs as $type => $diff ) {
			/** @var AtomicDiffOp[] $diffs */
			$typeDiffs = array();
			$typeDiffs['added'] = $diff->getAdditions();
			$typeDiffs['removed'] = $diff->getRemovals();
			$typeDiffs['changed'] = $diff->getChanges();

			$typeSummaryParts = array();
			foreach ( $typeDiffs as $typeType => $typeDiff ) {
				if( !empty( $typeDiff ) ) {
					$typeSummaryParts[] = $typeType . ' ' . count( $typeDiff ) . ' ' . $type;
				}
			}

			if( !empty( $typeSummaryParts ) ) {
				$summaryParts[] = implode( ', ', $typeSummaryParts );
			}

		}

		if ( !empty( $summaryParts ) ) {
			return implode( ': ', $summaryParts );
		}

		return 'Could not create a good summary.....';

	}

}
