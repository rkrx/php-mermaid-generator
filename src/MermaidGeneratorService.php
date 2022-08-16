<?php

namespace Kir\MermaidGenerator;

use Generator;
use Kir\ClassFinder\ClassFinder;
use ReflectionClass;

class MermaidGeneratorService {
	/**
	 * @param string $directory
	 * @return Generator<string>
	 */
	public function generateLines(string $directory) {
		$refMermaidDoc = new ReflectionClass(MermaidDoc::class);
		
		$files = ClassFinder::findClassesFromDirectory($directory);
		foreach($files->getFiles() as $classFile) {
			$filepath = $classFile->getFilepath();
			
			$content = file_get_contents($filepath);
			if($content === false) {
				continue;
			}
			
			if(!str_contains($content, $refMermaidDoc->getShortName())) {
				continue;
			}
			
			foreach($classFile->getClassNames() as $className) {
				require $classFile->getFilepath();
				
				/** @var class-string $className */
				$refClass = new ReflectionClass($className);
				foreach($refClass->getMethods() as $refMethod) {
					foreach($refMethod->getAttributes() as $refAttribute) {
						$instance = $refAttribute->newInstance();
						if($instance instanceof MermaidDoc) {
							$className = strtr($refClass->getName(), ['\\' => '__']);
							foreach($instance->docs as $doc) {
								$doc = strtr($doc, ['{METHOD}' => sprintf('%s___%s', $className, $refMethod->getName())]);
								$doc = htmlentities($doc, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8', false);
								$doc = strtr($doc, ['&lt;' => '<', '&gt;' => '>']);
								yield $doc;
							}
						}
					}
				}
			}
		}
	}
}
