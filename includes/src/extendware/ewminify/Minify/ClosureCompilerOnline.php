<?php
class Minify_ClosureCompilerOnline {

    static public function minifyJs($js, $options = array())
    {
    	$minifiedJs = null;
    	$allowUrlFopen = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
        if ($allowUrlFopen) {
			$minifiedJs = file_get_contents('http://closure-compiler.appspot.com/compile', false, stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query(array(
					  'js_code' => $js,
					  'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
					  'output_format' => 'text',
					  'output_info' => 'compiled_code',
                      'language' => 'ECMASCRIPT5',
                      'warning_level' => 'QUIET',
					), null, '&'),
                    'max_redirects' => 0,
                    'timeout' => 30,
                )
			)));
			if ($minifiedJs === false) $minifiedJs = null;
        } else {
	    	$ch = curl_init('http://closure-compiler.appspot.com/compile');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
				'js_code' => $js,
				'compilation_level' => 'SIMPLE_OPTIMIZATIONS',
				'output_format' => 'text',
				'output_info' => 'compiled_code',
 				'language' => 'ECMASCRIPT5',
				'warning_level' => 'QUIET',
			)));
			
			$minifiedJs = curl_exec($ch);

			if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) $minifiedJs = null;
        }
        $minifiedJs = preg_replace('/^\s*\/\*.*?\*\//s', '', $minifiedJs, 1);
		return trim($minifiedJs);
    }
}

