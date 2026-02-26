USE agit_aams;

-- Fix students that were incorrectly set to approved (matric_no NULL = should be pending)
UPDATE students SET approval_status = 'pending' WHERE matric_no IS NULL AND approval_status = 'approved';
