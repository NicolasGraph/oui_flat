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
 * Form partial iterator.
 *
 * @see Oui_Flat_TemplateIterator
 */

class Oui_Flat_FormIterator extends Oui_Flat_TemplateIterator
{
    /**
     * {@inheritdoc}
     */

    public function getTemplateName()
    {
        $filename = pathinfo($this->getFilename(), PATHINFO_FILENAME);
        
        return pathinfo($filename, PATHINFO_EXTENSION) ?: $filename;
    }

    /**
     * Gets the template type.
     *
     * If the file is named as:
     *
     * <code>
     * misc.filename.ext
     * </code>
     *
     * The 'misc' would be used as the type. Alternatively, the type can be
     * left out from the name and set with the parent directory name:
     *
     * <code>
     * misc/filenname.ext
     * </code>
     *
     * The 'misc' would be used as the type.
     *
     * If no type is specified at all, or it's not a valid type recognized by
     * Textpattern, it defaults to 'misc'.
     *
     * @return string
     */

    public function getTemplateType()
    {
        $types = get_form_types();
        $types = array_keys($types);
        $type = pathinfo(pathinfo($this->getFilename(), PATHINFO_FILENAME), PATHINFO_FILENAME);

        if (in_array($type, $types)) {
            return $type;
        }

        $type = basename($this->getPath());

        if (in_array($type, $types)) {
            return $type;
        }

        return 'misc';
    }
}
