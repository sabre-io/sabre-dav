<?php

/**
 * Principal Collection interface.
 *
 * Implement this interface to ensure that your principal collection can be
 * searched using the principal-property-search REPORT.
 *
 * @package Sabre
 * @subpackage DAVACL
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
interface Sabre_DAVACL_IPrincipalCollection extends Sabre_DAV_INode {

    /**
     * This method is used to search for principals matching a set of
     * properties.
     *
     * This search is specifically used by RFC3744's principal-property-search
     * REPORT. You should at least allow searching on
     * http://sabredav.org/ns}email-address.
     *
     * The actual search should be a unicode-non-case-sensitive search. The
     * keys in searchProperties are the WebDAV property names, while the values
     * are the property values to search on.
     *
     * If multiple properties are being searched on, the search should be
     * AND'ed.
     *
     * This method should simply return a list of 'child names', which may be
     * used to call $this->getChild in the future.
     *
     * @param array $searchProperties
     * @return array
     */
    function searchPrincipals(array $searchProperties);

}
