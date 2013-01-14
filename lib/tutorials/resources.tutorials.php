<?php
	function easyreservations_resources_tutorial() {
		$handler = array('#wpbody-content > h2','#add-new-h2', '#post-view', 'img[name="copylink"]:first', 'a[name="thelink"]:first');
		$content = array(
				'<h3>Resources</h3><p>Resources represent the things that your guest can resevate. They can be rooms, bungalows, scooters, tours, events or even humans. Each reservation must be attached to a resource, but can’t have multiple.</p>',
				'<h3>Add resource</h3><p>Click here to add a new resource.<p>',
				'<h3>Post view</h3><p>Technically resources are custom post types. All post releated option can be found here.</p>',
				'<h3>Copy resrouce</h3><p>You can also copy the resources settings and filters to minimize your work.</p>',
				'<h3>Edit resource</h3><p>Click here to edit the resource.</p>',
		);
		$offset = array(' 0', '-75 0', '-75 0', '-346 10', '-75 0');
		$at = array('', 'top', 'top', 'right', 'top');
		$execute = array('', '', '', '', 'window.location = jQuery(\'a[name="thelink"]:first\').attr(\'href\');');
		echo easyreservations_execute_pointer(5, $handler, $content, $offset, $at, $execute);
	}
?>