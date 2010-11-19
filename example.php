<?php

	// Hotpads class instance
	$hp = new Hotpads();
	
	// foreach property, create a new property element and append it to the master list

		// new prop data
		$prop = new HotpadProperty();
	
		$prop->type = 'SALE'; // SALE, CORPORATE, RENTAL, VACATION
		$prop->price_freq = 'ONCE'; // ONCE, MONTH
		$prop->property_type = 'HOUSE'; // HOUSE, CONDO, TOWNHOUSE, LAND

		// Main data
		$prop->unit				= '';
		$prop->name				= '';
		$prop->street			= '';
		$prop->city				= '';
		$prop->state			= '';
		$prop->zip				= '';
		$prop->contact_name		= '';
		$prop->contact_email	= '';
		$prop->contact_phone	= '';
		$prop->contact_fax		= '';
		$prop->description		= '';
		$prop->website_url		= '';
		$prop->sqft				= '';


		$prop->price	 		= '';
		$prop->bedrooms	 		= '';
		$prop->full_bath 		= '';
		$prop->half_bath 		= '';
		$prop->hoa_maint 		= '';
		$prop->price	 		= '';
		$prop->vr_url 			= '';
		$prop->photos			= array('url','url');

	$hp->addProperty($prop);
	
	// output the final xml via:
	print $hp->xml();

?>