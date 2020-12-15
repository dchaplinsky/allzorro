<?php
// src/Contract.php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Contracts")
 */
class Contract {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 */
	public $id;

	/**
	 * @ORM\Column(type="datetimetz", nullable=true)
	 */
	public $start_date;

	/**
	 * @ORM\Column(type="datetimetz", nullable=true)
	 */
	public $end_date;

	/**
	 * @ORM\Column(type="datetimetz")
	 */
	public $date_modified;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	public $identifier_id;
	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	public $identifier_legal_name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	public $contract_id;

	/**
	 *
	 * @ORM\Column(type="json_document", options={"jsonb": true})
	 */
	public $payload;
}