<?php
class Minify_ClosureCompiler {

    /**
     * Filepath of the Closure Compiler jar file. This must be set before
     * calling minifyJs() or minifyCss().
     *
     * @var string
     */
    static public $jarFile = null;
    
    /**
     * Writable temp directory. This must be set before calling minifyJs()
     * or minifyCss().
     *
     * @var string
     */
    static public $tempDir = null;
    
    /**
     * Filepath of "java" executable (may be needed if not in shell's PATH)
     *
     * @var string
     */
    static public $javaExecutable = 'java';
    
    /**
     * Minify a Javascript string
     * 
     * @param string $js
     * 
     * @param array $options (verbose is ignored)
     * 
     * @see http://www.julienlecomte.net/yuicompressor/README
     * 
     * @return string 
     */
    static public function minifyJs($js, $options = array())
    {
    	if (isset($options['warning_level']) === false) {
    		$options['warning_level'] = 'QUIET';
    	}
    	
        return self::_minify('js', $js, $options);
    }
    
    private static function _minify($type, $content, $options)
    {
        self::_prepare();
        if (! ($tmpFile = tempnam(self::$tempDir, 'clo_'))) {
            throw new Exception('Minify_ClosureCompiler : could not create temp file.');
        }
        file_put_contents($tmpFile, $content);
        @chmod($tmpFile, 0777);
        exec(self::_getCmd($options, $type, $tmpFile), $output);
        $output = @file_get_contents($tmpFile . '.output');
        
        @unlink($tmpFile);
        @unlink($tmpFile . '.output');
        
        // remove header comment that closure sometimes adds
       $output = preg_replace('/^\s*\/\*.*?\*\//s', '', $output, 1);
        return trim($output);
    }
    
    private static function _getCmd($userOptions, $type, $tmpFile)
    {	
        $o = array_merge(
            array(
                'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
            	'summary_detail_level' => 0,
            )
            ,$userOptions
        );
        $cmd = escapeshellarg(self::$javaExecutable) . ' -Xmx768m -jar ' . escapeshellarg(self::$jarFile);
        if ($type === 'js') {
            foreach (array('compilation_level', 'preserve-semi', 'disable-optimizations', 'warning_level', 'summary_detail_level') as $opt) {
                if (array_key_exists($opt, $o)) {
                    $cmd .= ' --' . $opt . ' ' . $o[$opt];
                }
            }
        }
        return $cmd . ' --third_party --js ' . escapeshellarg($tmpFile) . ' --js_output_file ' . escapeshellarg($tmpFile . '.output');
    }
    
    private static function _prepare()
    {
        if (! is_file(self::$jarFile) 
            || ! is_dir(self::$tempDir)
            || ! is_writable(self::$tempDir)
        ) {
            throw new Exception('Minify_ClosureCompiler : $jarFile and $tempDir must be set.');
        }
    }
}

