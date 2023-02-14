<?php

/**
 * @defgroup plugins_pubIds_edn EDN Pub ID Plugin
 */

/**
 * @file plugins/pubIds/edn/index.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_pubIds_edn
 * @brief Wrapper for EDN plugin.
 *
 */
require_once('EDNPubIdPlugin.inc.php');

return new EDNPubIdPlugin();


