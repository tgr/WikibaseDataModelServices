<?php

namespace Wikibase\DataModel\Services\Lookup;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Services\Entity\EntityRedirectResolvingDecorator;

/**
 * Implementation of EntityLookup that opaquely resolves one level
 * of redirects when looking up entities.
 *
 * @since 1.2
 *
 * @licence GNU GPL v2+
 * @author Daniel Kinzler
 */
class RedirectResolvingEntityLookup implements EntityLookup {

	/**
	 * An EntityRedirectResolvingDecorator wrapping and emulating a EntityLookup.
	 *
	 * @note This does not formally implement EntityLookup!
	 *
	 * @var EntityLookup
	 */
	protected $lookup;

	/**
	 * @param EntityLookup $lookup The lookup to use
	 */
	public function __construct( EntityLookup $lookup ) {
		$this->lookup = new EntityRedirectResolvingDecorator( $lookup );
	}

	/**
	 * @see EntityLookup::getEntity
	 *
	 * If the given entity ID points to a redirect, that redirect is resolved and the
	 * target entity returned.
	 *
	 * Callers can detect the presence of a redirect by comparing the ID of the returned
	 * Entity with the request ID.
	 *
	 * @param EntityId $entityId
	 *
	 * @return EntityDocument|null
	 * @throws EntityLookupException
	 */
	public function getEntity( EntityId $entityId ) {
		return $this->lookup->getEntity( $entityId );
	}

	/**
	 * @see EntityLookup::hasEntity
	 *
	 * If the given entity ID points to a redirect, that redirect is resolved and the
	 * existence of the target entity is checked.
	 *
	 * @param EntityId $entityId
	 *
	 * @return bool
	 */
	public function hasEntity( EntityId $entityId ) {
		return $this->lookup->hasEntity( $entityId );
	}

}
