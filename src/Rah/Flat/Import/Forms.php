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
 * Imports form partials.
 */

class Oui_Flat_Import_Forms extends oui_flat_Import_Base
{

    /**
     * {@inheritdoc}
     */

    public function getPanelName()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */

    public function getTableName()
    {
        return 'txp_form';
    }

    /**
     * {@inheritdoc}
     */

    public function importTemplate(oui_flat_TemplateIterator $file)
    {
        safe_upsert(
            $this->getTableName(),
            "Form = '".doSlash($file->getTemplateContents())."',
            type = '".doSlash(substr($this->directory, strrpos($this->directory, '/') + 1))."'",
            "name = '".doSlash($file->getTemplateName())."'"
        );
    }

    public function dropRemoved(oui_flat_TemplateIterator $template)
    {
        $name = array();

        while ($template->valid()) {
            $name[] = "'".doSlash($template->getTemplateName())."'";
            $template->next();
        }

        $formtype = substr($this->directory, strrpos($this->directory, '/') + 1);

        if ($name) {
            safe_delete($this->getTableName(), 'type = "'.doSlash($formtype).'" && name not in ('.implode(',', $name).')');
        } else {
            safe_delete($this->getTableName(), 'type = "'.doSlash($formtype).'"');
        }
    }
}
