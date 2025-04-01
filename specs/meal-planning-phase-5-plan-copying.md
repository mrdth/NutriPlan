# Meal Planning - Phase 5: Plan Copying

## Overview
This phase adds the ability for users to copy existing meal plans, supporting recurring meal planning patterns and reducing repetitive work. This enables a practical workflow for users who tend to reuse meal plans with modifications.

## Core Functionality

### Plan Copying
- Users can create a new plan by copying an existing one
- When copying, users specify:
  - New start date
  - Whether to adjust for the number of people (optional)
- All recipes, day assignments, and "to cook" flags are duplicated

### Copy Customization
- After copying, users can modify the new plan independently
- Option to rename the copied plan during creation
- Clear indication that a plan was created from a copy

## Database Structure
- No new table fields required
- Leverages existing database structure
- Implements copy functionality at the application level

## Implementation Details

### Backend Requirements
- Controller methods for plan copying operations
- Logic to duplicate all related recipe assignments
- Services to handle complex copying operations
- API endpoints for all copy-related features

### Frontend Requirements
- "Copy Plan" button on meal plan detail and index pages
- Copy configuration modal with:
  - Start date picker for new plan
  - Number of people adjustment (optional)
  - Name field for new plan
- Success confirmation and navigation to new plan
- Interface for selective copying of days/recipes

## Testing Criteria
- Complete plan copying works correctly with all relationships preserved
- Day assignments maintain proper sequence in the new plan
- "To cook" flags transfer correctly
- Selective copying functions as expected
- Edge cases (empty plans, maximum day ranges) are handled properly

## Value Proposition
This phase significantly reduces repetitive work for users who follow recurring meal patterns. By allowing plans to be copied and modified, users can iterate on successful meal plans over time, creating a more efficient meal planning workflow and encouraging ongoing use of the feature. 