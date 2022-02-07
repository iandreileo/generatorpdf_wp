<?php
/*
Plugin Name: generate-pdf-uhs
Description: Modul de generat PDF-uri.
*/

require('include/fpdf.php');
require('include/fpdi/src/autoload.php');
require('include/fpdi/src/Fpdi.php');

function generate_pdf($data) {

$class = "FPDI";
$pdf = new \setasign\Fpdi\Fpdi();
// add a page
$pdf->AddPage();  
// set the sourcefile  
$pdf->setSourceFile(__DIR__ . '/Formular_donatie.pdf');  

// import page 1
$tplIdx = $pdf->importPage(1);
$size = $pdf->getTemplateSize($tplIdx);
// print_r($size);
$pdf ->useTemplate($tplIdx, null, null, $size['width'], 310, FALSE);
// now write some text above the imported page
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(255, 0, 0);

$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0); // RGB

// Prenume
$pdf->SetXY(25, 65); // X start, Y start in mm
$text = $data['prenume'];
$pdf->Write(0, $text);

// Nume 
$pdf->SetXY(25, 57); // X start, Y start in mm
$text = $data['nume'];
$pdf->Write(0, $text);

// Initala 
$pdf->SetXY(105, 57); // X start, Y start in mm
$text = $data['initialaTatalui'];
$pdf->Write(0, $text);

// Strada 
$pdf->SetXY(25, 73.5); // X start, Y start in mm
$text = $data['strada'];
$pdf->Write(0, $text);

// Numar 
$pdf->SetXY(101, 73.5); // X start, Y start in mm
$text = $data['numar'];
$pdf->Write(0, $text);

// Email 
$pdf->SetXY(130, 70); // X start, Y start in mm
$text = $data['email'];
$pdf->Write(0, $text);

// Telefon 
$pdf->SetXY(130, 80); // X start, Y start in mm
$text = $data['telefon'];
$pdf->Write(0, $text);


// Bloc 
$pdf->SetXY(17, 82); // X start, Y start in mm
$text = $data['bloc'];
$pdf->Write(0, $text);

// Scara 
$pdf->SetXY(38, 82); // X start, Y start in mm
$text = $data['scara'];
$pdf->Write(0, $text);

// Etaj 
$pdf->SetXY(52, 82); // X start, Y start in mm
$text = $data['et'];
$pdf->Write(0, $text);

// Ap 
$pdf->SetXY(65, 82); // X start, Y start in mm
$text = $data['ap'];
$pdf->Write(0, $text);

// Judet 
$pdf->SetXY(90, 82); // X start, Y start in mm
$text = $data['judet'];
$pdf->Write(0, $text);

// Localiate 
$pdf->SetXY(25, 90); // X start, Y start in mm
$text = $data['localitate'];
$pdf->Write(0, $text);

// Cod Postal
$pdf->SetXY(92, 90); // X start, Y start in mm
$text = $data['codPostal'];
$pdf->Write(0, $text);

// CNP
// $data['cnp'] = '5010118394072';
for ($i = 0; $i < strlen($data['cnp']); $i++) {
    // echo $data['cnp'][$i];
    $pdf->SetXY(118 + ($i * 6), 60); // X start, Y start in mm
    $text = $data['cnp'][$i];
    $pdf->Write(0, $text);
}

// Timestamp
$filename = rand() . '_' . time() . '.pdf';

$directory = plugin_dir_path( __FILE__ ). 'pdf_forms/' . $filename;

// echo $directory;
// $pdf->Output
$pdf->Output('F', $directory);
return $directory;
}

// add_action('admin_init', 'generate_pdf'); 

    add_action( 'wpcf7_before_send_mail', 'wpcf7_do_something_else_with_the_data', 90, 1 );
    
    function wpcf7_do_something_else_with_the_data( $WPCF7_ContactForm ){
    
    
        // $filename = generate_pdf();
    
        // Submission object, that generated when the user click the submit button.
        $submission = WPCF7_Submission :: get_instance();
    
        if ( $submission ){
            $posted_data = $submission->get_posted_data();      
            if ( empty( $posted_data ) ){ return; }
            
            // Test
            // $filename = generate_pdf($data);
            
            // Got name data
            $name_data = $posted_data['test'];
    
            // Do my code with this name

            // Got e-mail text
            $mail = $WPCF7_ContactForm->prop( 'mail' );
    
            // Replace "[s2-name]" field inside e-mail text
            $new_mail = str_replace( '[test]', $filename, $mail );
    
            // Set
            $WPCF7_ContactForm->set_properties( array( 'mail' => $new_mail ) );
            

            return $WPCF7_ContactForm;
        }
    }

add_filter( 'wpcf7_mail_components', 'mycustom_wpcf7_mail_components', 10,2 );

function mycustom_wpcf7_mail_components( $mail_component, $contact_form ) {
        $mail_component['subject']; //email subject
        $mail_component['sender']; //sender field (from)
        $mail_component['body']; //email body
        $mail_component['recipient']; //email recipient (to)
        $mail_component['additional_headers']; //email headers, cc:, bcc:, reply-to:
        $mail_component['attachments']; //file attachments if any
        $key_values = array();
        $tags = $contact_form->scan_form_tags(); //get your form tags
        
        foreach($tags as $tag){
            $field_name  = $tag['name'];
            if(isset($_POST[$field_name]) && !empty($_POST[$field_name])){
              //get all the submitted fields form your form
                $key_values[$field_name] = $_POST[$field_name]; 
            }
        }

    
    $filename = generate_pdf($key_values);
    $mail_component['attachments'][] = $filename;

    return $mail_component;
}
?>
