<?php

namespace Kir\MermaidGenerator;

use Attribute;

#[Attribute]
class MermaidDoc {
	/** @var string[] */
	public array $docs;
	
	public function __construct(string ...$doc) {
		$this->docs = $doc;
	}
}
