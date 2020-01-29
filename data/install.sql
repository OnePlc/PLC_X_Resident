--
-- Base Table
--
CREATE TABLE `resident` (
  `Resident_ID` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `resident`
  ADD PRIMARY KEY (`Resident_ID`);

ALTER TABLE `resident`
  MODIFY `Resident_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Permissions
--
INSERT INTO `permission` (`permission_key`, `module`, `label`, `nav_label`, `nav_href`, `show_in_menu`) VALUES
('add', 'OnePlace\\Resident\\Controller\\ResidentController', 'Add', '', '', 0),
('edit', 'OnePlace\\Resident\\Controller\\ResidentController', 'Edit', '', '', 0),
('index', 'OnePlace\\Resident\\Controller\\ResidentController', 'Index', 'Residents', '/resident', 1),
('list', 'OnePlace\\Resident\\Controller\\ApiController', 'List', '', '', 1),
('view', 'OnePlace\\Resident\\Controller\\ResidentController', 'View', '', '', 0);

--
-- Form
--
INSERT INTO `core_form` (`form_key`, `label`, `entity_class`, `entity_tbl_class`) VALUES
('resident-single', 'Resident', 'OnePlace\\Resident\\Model\\Resident', 'OnePlace\\Resident\\Model\\ResidentTable');

--
-- Index List
--
INSERT INTO `core_index_table` (`table_name`, `form`, `label`) VALUES
('resident-index', 'resident-single', 'Resident Index');

--
-- Tabs
--
INSERT INTO `core_form_tab` (`Tab_ID`, `form`, `title`, `subtitle`, `icon`, `counter`, `sort_id`, `filter_check`, `filter_value`) VALUES ('resident-base', 'resident-single', 'Resident', 'Base', 'fas fa-cogs', '', '0', '', '');

--
-- Buttons
--
INSERT INTO `core_form_button` (`Button_ID`, `label`, `icon`, `title`, `href`, `class`, `append`, `form`, `mode`, `filter_check`, `filter_value`) VALUES
(NULL, 'Save Resident', 'fas fa-save', 'Save Resident', '#', 'primary saveForm', '', 'resident-single', 'link', '', ''),
(NULL, 'Edit Resident', 'fas fa-edit', 'Edit Resident', '/resident/edit/##ID##', 'primary', '', 'resident-view', 'link', '', ''),
(NULL, 'Add Resident', 'fas fa-plus', 'Add Resident', '/resident/add', 'primary', '', 'resident-index', 'link', '', '');

--
-- Fields
--
INSERT INTO `core_form_field` (`Field_ID`, `type`, `label`, `fieldkey`, `tab`, `form`, `class`, `url_view`, `url_ist`, `show_widget_left`, `allow_clear`, `readonly`, `tbl_cached_name`, `tbl_class`, `tbl_permission`) VALUES
(NULL, 'text', 'Name', 'label', 'resident-base', 'resident-single', 'col-md-3', '/resident/view/##ID##', '', 0, 1, 0, '', '', '');

--
-- Default Widgets
--
INSERT INTO `core_widget` (`Widget_ID`, `widget_name`, `label`, `permission`) VALUES
(NULL, 'resident_dailystats', 'Resident - Daily Stats', 'index-Resident\\Controller\\ResidentController'),
(NULL, 'resident_taginfo', 'Resident - Tag Info', 'index-Resident\\Controller\\ResidentController');

COMMIT;