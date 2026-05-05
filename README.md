# Delete Production Submissions Plugin for OJS 3.4+

## Overview
The **Delete Production Submissions** plugin is an administrative maintenance tool designed for Open Journal Systems (OJS) 3.4. It provides Journal Managers with a centralized interface to bulk-delete submissions that are no longer needed, helping to keep the database clean and manageable.

Despite its name, the plugin is a versatile cleanup tool that can target multiple types of inactive or unwanted submissions.

## Key Features
- **Multiple Target Sets**:
  - **Production Stage**: Delete submissions that have reached the production stage but were never published.
  - **Incomplete Submissions**: Clean up submissions that authors started but never finished.
  - **Declined Submissions**: Bulk-remove submissions that have been formally declined.
- **Flexible Deletion Modes**:
  - **Time-based**: Delete submissions based on days of inactivity (e.g., older than 90 days).
  - **Selective**: Manually select specific submissions from a list for removal.
  - **All**: Remove all submissions within the chosen target set.
- **Safety First**: Includes a mandatory confirmation step to prevent accidental data loss.

## Installation

### Manual Installation
1. Download the plugin files.
2. Extract and place the folder into the `plugins/generic/` directory of your OJS installation.
3. Rename the folder to `deleteproductionsubmissions` if it isn't already.
4. Log in as a Journal Manager or Site Administrator.
5. Navigate to **Settings > Website > Plugins**.
6. Find "Delete Production Submissions" in the list and check the box to **Enable** it.

## Usage Instructions

1. **Accessing the Tool**:
   - Go to **Settings > Website > Plugins**.
   - Locate the **Delete Production Submissions** plugin under the **Generic Plugins** category.
   - Click the blue arrow next to the plugin name and select **Delete Submissions**.

2. **Configuration**:
   - **Target Set**: Choose whether you want to delete Production, Incomplete, or Declined submissions.
   - **Selection Mode**:
     - **Inactive for X days**: Enter the number of days of inactivity to filter submissions (Default: 90).
     - **Select Submissions**: This will display a list of available submissions where you can check specific ones.
     - **Delete All**: Prepares all submissions in the target set for deletion.

3. **Execution**:
   - Click the **Preview** button.
   - A confirmation area will appear. Review your choices carefully.
   - Check the **"I confirm that I want to permanently delete these submissions"** box.
   - Click the **Delete** button to finalize the action.

## Important Warnings
> [!CAUTION]
> **IRREVERSIBLE ACTION**: Deletion of submissions is permanent and cannot be undone. All associated files, metadata, and history will be purged from the database. Always ensure you have a recent database backup before performing bulk deletions.

## License
Distributed under the GNU GPL v3. For full terms, see the file `docs/COPYING`.

## Support
For issues or feature requests, please contact the repository maintainers.
