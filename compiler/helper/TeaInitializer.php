<?php
namespace Tea;

class TeaInitializer
{
	private $uri;
	private $domain;

	public function __construct(string $uri)
	{
		$uri = FileHelper::normalize_path($uri);
		$this->set_unit_uri(trim($uri, DS));
	}

	public function process()
	{
		$unit_dir = $this->uri;

		// create dir
		is_dir($unit_dir . '/src') or FileHelper::mkdir($unit_dir . '/src');

		// the header file
		$header_file = $unit_dir . '/__package.th';
		$ns = strtr($this->uri, '/', '\\');
		file_exists($header_file) or file_put_contents($header_file, "\n" . _NAMESPACE . " {$ns}\n\n");

		// the main.tea
		$main_file = $unit_dir . '/src/main.tea';
		file_exists($main_file) or file_put_contents($main_file, "\necho 'Hello!'\n\n// end\n");

		// the editorconfig settings
		$editorconfig_file = $this->domain . '/.editorconfig';
		file_exists($editorconfig_file) or copy(TEA_BASE_PATH . '.editorconfig', $editorconfig_file);

		// the gitattributes settings
		$gitattributes_file = $this->domain . '/.gitattributes';
		file_exists($gitattributes_file) or copy(TEA_BASE_PATH . '.gitattributes', $gitattributes_file);
	}

	public function set_unit_uri(string $uri)
	{
		$components = explode(DS, $uri);

		$domain = $components[0];
		if (!self::check_unit_uri_domain($domain)) {
			throw new Exception("Invalid URI name '$uri' for declare Unit.");
		}

		$levels = count($components);
		if ($levels > _NS_LEVELS_MAX) {
			throw new Exception(sprintf("It's too many namespace levels, the max levels is %d.", _NS_LEVELS_MAX));
		}

		for ($i = 1; $i < $levels; $i++) {
			$sub_name = $components[$i];
			if (!TeaHelper::is_subnamespace_name($sub_name)) {
				throw new Exception("Invalid URI name '$uri' for declare Unit.");
			}
		}

		$this->uri = $uri;
		$this->domain = $domain;
	}

	private static function check_unit_uri_domain(string $domain)
	{
		$components = explode(_DOT, $domain);
		for ($i = 0; $i < count($components); $i++) {
			$name = $components[$i];
			if (!TeaHelper::is_domain_component($name)) {
				return false;
			}
		}

		return true;
	}
}

