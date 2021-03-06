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
 * Imports preferences.
 */

class Oui_Flat_Import_Prefs extends Oui_Flat_Import_Sections
{
    /**
     * {@inheritdoc}
     */

    public function getPanelName()
    {
    }

    /**
     * {@inheritdoc}
     */

    public function getTableName()
    {
        return 'txp_prefs';
    }

    /**
     * {@inheritdoc}
     */

    public function importTemplate(Oui_Flat_TemplateIterator $file)
    {
        $sql = array();
        $where = "name = '".doSlash($file->getTemplateName())."' and user_name = ''";

        if ($file->getExtension() === 'json') {
            foreach ($file->getTemplateJSONContents() as $key => $value) {
                if (in_array(strtolower((string) $key), $this->getTableColumns(), true)) {
                    $sql[] = $this->formatStatement($key, $value);
                }
            }
        } else {
            $sql[] = 'val = "' . doSlash($file->getTemplateContents()) . '"';
        }

        return $sql && safe_update($this->getTableName(), implode(',', $sql), $where);
    }

    /**
     * {@inheritdoc}
     */

    public function dropRemoved(Iterator $template)
    {
    }
}
