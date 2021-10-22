
                               <select id="framework" name="framework[]" multiple class="form-control" 
                                                                data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'  >
                                <option value="">Select Exam</option>
<?php
if(!empty($results)){
foreach($results as $result){

        if (count($result)) {
                $html .= '<option value="">' . translate('select') . '</option>';
                foreach ($result as $row) {
                    if ($row['term_id'] != 0) {
                        $term = $this->db->select('name')->where('id', $row['term_id'])->get('exam_term')->row()->name;
                        $name = $row['name'] . ' (' . $term . ')';
                    } else {
                        $name = $row['name'];
                    }
                    $html .= '<option value="' . $row['id'] . '">' . $name . '</option>';
                }
            } else {
                $html .= '<option value="">' . translate('no_information_available') . '</option>';
            }
        } else {
            $html .= '<option value="">' . translate('select_branch_first') . '</option>';
        }
        echo $html;

}
}
?>
                                </select>
                            