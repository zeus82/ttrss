<?php
class Dlg extends Handler_Protected {
	private $param;
    private $params;

    function before($method) {
		if (parent::before($method)) {
			header("Content-Type: text/html"); # required for iframe

			$this->param = ($_REQUEST["param"] ?? false);
			return true;
		}
		return false;
	}

	function printTagCloud() {
		print "<div class='panel text-center'>";

		// from here: http://www.roscripts.com/Create_tag_cloud-71.html

		$sth = $this->pdo->prepare("SELECT tag_name, COUNT(post_int_id) AS count
			FROM ttrss_tags WHERE owner_uid = ?
			GROUP BY tag_name ORDER BY count DESC LIMIT 50");
		$sth->execute([$_SESSION['uid']]);

		$tags = array();

		while ($line = $sth->fetch()) {
			$tags[$line["tag_name"]] = $line["count"];
		}

        if(count($tags) == 0 ){ return; }

		ksort($tags);

		$max_size = 32; // max font size in pixels
		$min_size = 11; // min font size in pixels

		// largest and smallest array values
		$max_qty = max(array_values($tags));
		$min_qty = min(array_values($tags));

		// find the range of values
		$spread = $max_qty - $min_qty;
		if ($spread == 0) { // we don't want to divide by zero
				$spread = 1;
		}

		// set the font-size increment
		$step = ($max_size - $min_size) / ($spread);

		// loop through the tag array
		foreach ($tags as $key => $value) {
			// calculate font-size
			// find the $value in excess of $min_qty
			// multiply by the font-size increment ($size)
			// and add the $min_size set above
			$size = round($min_size + (($value - $min_qty) * $step));

			$key_escaped = str_replace("'", "\\'", (string)$key);

			echo "<a href=\"#\" onclick=\"Feeds.open({feed:'$key_escaped'}) \" style=\"font-size: " .
				$size . "px\" title=\"$value articles tagged with " .
				$key . '">' . $key . '</a> ';
		}



		print "</div>";

		print "<footer class='text-center'>";
		print "<button dojoType='dijit.form.Button'
			onclick=\"return CommonDialogs.closeInfoBox()\">".
			__('Close this window')."</button>";
		print "</footer>";

	}
}
