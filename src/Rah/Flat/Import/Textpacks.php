<?php

/*
 * oui_flat - Flat templates for Textpattern CMS
 * https://github.com/gocom/oui_flat
 *
 * Copyright (C) 2015 Jukka Svahn
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
 * Imports Textpacks.
 */

class Oui_Flat_Import_Textpacks extends oui_flat_Import_Sections
{

    /**
     * {@inheritdoc}
     */

    public function getPanelName()
    {
        return 'lang';
    }

    /**
     * {@inheritdoc}
     */

    public function getTableName()
    {
        return 'txp_lang';
    }

    /**
     * {@inheritdoc}
     */

    public function importTemplate(oui_flat_TemplateIterator $file)
    {
        global $DB;

        foreach ($file->getTemplateJSONContents() as $event => $array) {
            foreach ($array as $key => $value) {
                $set = $this->formatStatement('event', $event).', '.$this->formatStatement('data', $value);
                $where = "lang = '".doSlash($file->getTemplateName())."' AND ".$this->formatStatement('name', $key);
                $r = safe_update($this->getTableName(), $set, $where);
                if ($r and (mysqli_affected_rows($DB->link) or safe_count($this->getTableName(), $where))) {
                    $r;
                } else {
                    $set .= ", owner = 'oui_flat_lang'";
                    $where = implode(', ', (preg_split("/ AND /", $where)));
                    safe_insert($this->getTableName(), join(', ', array($where, $set)));
                }
            }
        }
        return;
    }

    /**
     * Formats a SQL insert statement value.
     *
     * @param  string $field The field
     * @param  string $value The value
     * @return string
     */

    protected function formatStatement($field, $value)
    {
        if ($value === null) {
            return "`{$field}` = NULL";
        }

        if (is_bool($value) || is_int($value)) {
            return "`{$field}` = ".intval($value);
        }

        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return "`{$field}` = '".doSlash((string) $value)."'";
    }

    /**
     * {@inheritdoc}
     */

    public function dropRemoved(oui_flat_TemplateIterator $template)
    {

        while ($template->valid()) {
            $lang = "lang = '".doSlash($template->getTemplateName())."'";

            if ($lang) {
                foreach ($template->getTemplateJSONContents() as $event => $array) {
                    $name = array();
                    $event = "event = '".$event."'";
                    foreach ($array as $key => $value) {
                        $name[] = "'".doSlash($key)."'";
                    }
                    if ($name) {
                        safe_delete($this->getTableName(), $lang.' AND '.$event.' AND name not in ('.implode(',', $name).') AND owner = "oui_flat_lang"');
                    } else {
                        safe_delete($this->getTableName(), $lang.' AND '.$event.' AND owner = "oui_flat_lang"');
                    }
                }
            }

            $template->next();
        }
    }

    /**
     * {@inheritdoc}
     */

    public function dropPermissions()
    {
    }
}
