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
 * Imports form partials.
 */

class Oui_Flat_Import_Forms extends Oui_Flat_Import_Pages
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

    public function getEssentials()
    {
        return get_essential_forms();
    }

    /**
     * {@inheritdoc}
     */

    public function getTemplateIterator($directory)
    {
        return new RecursiveIteratorIterator(
            new Oui_Flat_FilterIterator(
                new Oui_Flat_FormIterator($directory)
            )
        );
    }

    /**
     * {@inheritdoc}
     */

    public function importTemplate(Oui_Flat_TemplateIterator $file)
    {
        safe_upsert(
            $this->getTableName(),
            "Form = '".doSlash($file->getTemplateContents())."',
            type = '".doSlash($file->getTemplateType())."'",
            "name = '".doSlash($file->getTemplateName())."'"
        );
    }
}
