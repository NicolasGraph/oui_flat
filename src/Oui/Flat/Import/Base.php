<?php

/*
 * oui_flat - Flat templates for Textpattern CMS
 * https://github.com/nicolasgraph/oui_flat
 *
 * Copyright (C) 2017 Jukka Svahn
 *
 * This file is part of oui_flat.
 *
 * oui_flat is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2.
 *
 * oui_flat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with oui_flat. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Base class for import definitions.
 *
 * To create a new importable template object, extend
 * this class or its theriatives.
 *
 * For instance the following would create a new import
 * definition using the Oui_Flat_Import_Pages as the
 * base:
 *
 * <code>
 * class Abc_My_Import_Definition extends Oui_Flat_Import_Pages
 * {
 *     public function getPanelName()
 *     {
 *         return 'abc_mypanel';
 *     }
 *     public function getTableName()
 *     {
 *         return 'abc_mytable';
 *     }
 * }
 * </code>
 *
 * It would automatically disable access to 'abc_mypanel' admin-side panel
 * and import items to 'abc_mytable' database table, consisting of 'name'
 * and 'user_html' columns, as with page templates and its txp_page table.
 *
 * To initialize the import, just create a new instance of the class. Pass
 * constructor the directory name the templates reside in the configured
 * templates directory.
 *
 * <code>
 * new Abc_My_Import_Definition('abc_mydirectory');
 * </code>
 */

abstract class Oui_Flat_Import_Base implements Oui_Flat_Import_ImportInterface
{
    /**
     * The directory.
     *
     * @var string
     */

    protected $directory;

    /**
     * An array of database table columns.
     *
     * @var array
     */

    private $columns = array();

    /**
     * {@inheritdoc}
     */

    public function getPanelName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */

    public function getTableName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */

    public function __construct($directory)
    {
        $this->directory = $directory;
        register_callback(array($this, 'init'), 'oui_flat.import_to_database');
        $this->dropPermissions();
    }

    /**
     * {@inheritdoc}
     */

    public function getTemplateIterator($directory)
    {
        return new RecursiveIteratorIterator(
            new Oui_Flat_FilterIterator(
                new Oui_Flat_TemplateIterator($directory)
            )
        );
    }

    /**
     * {@inheritdoc}
     */

    public function getDirectoryPath()
    {
        if ($this->directory && ($directory = get_pref('oui_flat_path'))) {
            $directory = txpath . '/' . $directory . '/' . $this->directory;
            return $directory;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */

    public function isEnabled()
    {
        if ($directory = $this->getDirectoryPath()) {
            return file_exists($directory) && is_dir($directory) && is_readable($directory);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */

    public function init()
    {
        if ($this->isEnabled()) {
            $templates = $this->getTemplateIterator($this->getDirectoryPath());

            foreach ($templates as $template) {
                if ($this->importTemplate($template) === false) {
                    throw new Exception('Unable to import ' . $template->getTemplateName());
                }
            }

            $this->dropRemoved($templates);
        }
    }

    /**
     * {@inheritdoc}
     */

    public function dropPermissions()
    {
        if (txpinterface === 'admin' && $this->isEnabled()) {
            unset($GLOBALS['txp_permissions'][$this->getPanelName()]);
        }
    }

    /**
     * {@inheritdoc}
     */

    public function getEssentials()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */

    public function getTableColumns()
    {
        if (!$this->columns) {
            $this->columns = doArray((array) @getThings('describe ' . safe_pfx($this->getTableName())), 'strtolower');
        }

        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */

    public function dropRemoved(Iterator $templates)
    {
        $name = array();

        foreach ($templates as $template) {
            $name[] = "'" . doSlash($template->getTemplateName()) . "'";
        }

        foreach ($this->getEssentials() as $template) {
            $name[] = "'" . doSlash((string) $template) . "'";
        }

        if ($name) {
            safe_delete($this->getTableName(), 'name not in (' . implode(',', $name) . ')');
        } else {
            safe_delete($this->getTableName(), '1 = 1');
        }
    }
}
