<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey_Answer;
use App\Models\Survey;
use App\Models\Standard;
use setasign\Fpdi\Fpdi;
use PDF;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\selfAssessmentExport; 
// include composer packages
//include "C:\Users\JALAL\Desktop\laravel projects\acsod/vendor\autoload.php";
require_once('C:\Users\JALAL\Desktop\laravel projects\acsod/vendor\autoload.php');
require_once('C:\Users\JALAL\Desktop\laravel projects\acsod\vendor\setasign\fpdf\makefont\makefont.php');
class reportGenerator extends Controller
{
  
public function viewCSV($id)
{
    $ngo=Survey::find($id)->join("ngos","ngos.id","surveys.ngo_id")->first("ngos.name_of_the_organization");
    $survey_answers = Survey_Answer::leftjoin('questions','survey_answers.question_id','questions.id')
    ->leftjoin('standards','standards.id','survey_answers.standard_id')
    ->leftjoin('not_applicable_comments','survey_answers.id','not_applicable_comments.answer_id')
    ->leftjoin('documents','documents.question_id','questions.id')
    ->where('survey_id',$id)->orderBy('standard_id')
    ->get(['survey_answers.standard_id','survey_answers.question_id','survey_answers.answer','standards.title as standard_title',
          'questions.title as question_title','not_applicable_comments.text','documents.name']);
    // return $survey_answers;
    // return Survey_Answer::leftjoin('not_applicable_comments','not_applicable_comments.id','survey_answers.comment_id')->where('survey_id',$id)->get();
     $survey_score= new Survey_Answer;  
    $csvExporter = new \Laracsv\Export();
  //  return $survey_score->get_scores_details_per_standards($survey_answers);
   // $csvExporter->build(,['Standard','Question','Answer','Score'])->download();
   // return Excel::download(, 'users.xlsx');
    //return $survey_answers;
    return Excel::download(new selfAssessmentExport($survey_score->get_scores_details_per_standards($survey_answers)), $ngo->name_of_the_organization."selfAssessmentreport.xlsx");
}


    public function viewReport($id)
    {

      $ngo_name=Survey::find($id)->first(['name_of_org','date_completed']);
      // Create new Landscape PDF
$pdf = new Fpdi();

// Reference the PDF you want to use (use relative path)
$pagecount = $pdf->setSourceFile('C:\Users\JALAL\Desktop\laravel projects\acsod\app\pdfTemplate\Report_new.pdf');
//MakeFont('C:\Users\JALAL\Desktop\laravel projects\acsod\app\Fonts\RobotoCondensed\RobotoCondensed-Regular.ttf','cp1252',false,true);
// Import the first page from the PDF and add to dynamic PDF

$tpl = $pdf->importPage(1);
$pdf->AddPage();

// Use the imported page as the template
$pdf->useTemplate($tpl);

// Set the default font to use
$pdf->AddFont('RobotoCondensed-Regular','','RobotoCondensed-Regular.php');
$pdf->AddFont('RobotoCondensed-Bold','','RobotoCondensed-Bold.php');
$pdf->SetFont('RobotoCondensed-Bold');

// adding a Cell using:
// $pdf->Cell( $width, $height, $text, $border, $fill, $align);

// First box - the user's Name
$pdf->SetTextColor(255,255,255);
$pdf->SetFontSize('20.0'); // set font size
$pdf->SetXY(15,105); // set the position of the box
$pdf->Cell(10,10,$ngo_name->name_of_org, 0, 0, 'L'); // add the text, align to Center of cell

$pdf->SetXY(10,260); // set the position of the box
$pdf->SetFontSize('24.5'); // set font size
$pdf->Cell(0,10, date('F j, Y',strtotime($ngo_name->date_completed))); // add the text, align to Center of cell

// Import the second page from the PDF and add to dynamic PDF
$tpl = $pdf->importPage(2);
$pdf->AddPage();
$pdf->useTemplate($tpl);

  // get all answers with the corresponding standards details 
  $survey_answers = Survey_Answer::join('questions','survey_answers.question_id','questions.id')
  ->join('standards','standards.id','survey_answers.standard_id')
  ->where('survey_id',$id)->orderBy('standard_id')
  ->get(['survey_answers.standard_id','survey_answers.question_id','survey_answers.answer','standards.title as standard_title',
        'questions.title as question_title','questions.improvement','standards.capacity_building']);

$data= new Survey_Answer;
$data_stats = $data->PDF_Data($survey_answers);

// Import the third page from the PDF and add to dynamic PDF
$tpl = $pdf->importPage(3);
$pdf->AddPage();
$pdf->useTemplate($tpl);
$total=0;
$Y=66;  
$pdf->SetFont('RobotoCondensed-Regular');
$pdf->SetLeftMargin(21);
$pdf->SetRightMargin(21);
foreach($data_stats as $standard_stats)
{
 $pdf->SetTextColor(9,33,64);
 $pdf->SetFontSize('9.1'); // set font size
 $pdf->setXY(162,$Y);
 $pdf->Cell(10,10,round(($total/count($data_stats)),2)."/100",0,0,'L');
 $total+=$standard_stats['score'];
 $Y+=11;
}
$pdf->setXY(162,178.3);
$pdf->Cell(10,10,($total/count($data_stats))."/100",0,0,'L');
//return $data_stats;
$pdf->SetFont('RobotoCondensed-Bold');
$count=0;// count the number of standard
foreach($data_stats as $standard_stats)
{
  $count++;
  $tpl = $pdf->importPage(4);
  $pdf->AddPage();
  $pdf->useTemplate($tpl);

  // set standard title
  $pdf->SetTextColor(255,255,255);
  $pdf->SetFontSize('14.0'); // set font size
  $pdf->SetXY(21,42); // set the position of the box
  $pdf->Cell(10,10,"STANDARD $count.".$standard_stats['standard'], 0, 0, 'L'); // add the text, align to Center of cell

  
  // fill standard stats
  $pdf->SetTextColor(9,33,64);
  $pdf->SetFontSize('30.3'); // set font size
  $pdf->SetXY(32,60); // set the position of the box
  $pdf->Cell(10,10,$standard_stats['Met'], 0, 0, 'L'); // add the text, align to Center of cell
  
  $pdf->SetX(65); // set the position of the box
  $pdf->Cell(10,10,$standard_stats['Partially_Met'], 0, 0, 'L'); // add the text, align to Center of cell

  $pdf->SetX(100); // set the position of the box
  $pdf->Cell(10,10,$standard_stats['Not_Met'], 0, 0, 'L'); // add the text, align to Center of cell

  $pdf->SetX(134); // set the position of the box
  $pdf->Cell(10,10,$standard_stats['Not_Applicable'], 0, 0, 'L'); // add the text, align to Center of cell
 
  $pdf->SetTextColor(58,186,131);// green color
  $pdf->SetFontSize('25.0'); // set font size
  $pdf->SetX(160); // set the position of the box
  $pdf->Cell(10,10,$standard_stats['score']."%", 0, 0, 'L'); // add the text, align to Center of cell
  
 
 $pdf->SetXY(21,100); // set the position of the box 
 if(!empty($standard_stats['Met_questions'])){
  // set the Met question 
  $pdf->SetTextColor(58,186,131);// green color 
  $pdf->SetFontSize('12'); // set font size
  $pdf->SetFont('RobotoCondensed-Bold');
  $pdf->Cell(10,10, 'Met', 0, 0, 'L'); // add the text, align to Center of cell
  
  $pdf->SetFont('RobotoCondensed-Regular');
  $pdf->SetTextColor(27,24,17);
  $pdf->SetFontSize('10'); // set font size
  $pdf->Ln(8); // set the position of the box
  $pdf->write(4,$standard_stats['Met_questions']);
  $pdf->Ln(4);
   }

  if(!empty($standard_stats['Partially_Met_questions'])){
    // set the Partially Met question
  $pdf->SetTextColor(58,186,131);// green color  
  $pdf->SetFontSize('12'); // set font size
  $pdf->SetX(21); // set the position of the box
  $pdf->SetFont('RobotoCondensed-Bold');
  $pdf->Cell(10,10, 'Partially Met', 0, 0, 'L'); // add the text, align to Center of cell
  
  $pdf->SetFont('RobotoCondensed-Regular');  
  $pdf->SetTextColor(27,24,17);
  $pdf->SetFontSize('10'); // set font size
  $pdf->Ln(8); // set the position of the box
  $pdf->write(4,$standard_stats['Partially_Met_questions']);
  $pdf->Ln(4);
  }  

  if(!empty($standard_stats['NotMet_questions'])){
  // set the Not Met question 
  $pdf->SetTextColor(58,186,131);// green color 
  $pdf->SetFontSize('12'); // set font size
  $pdf->SetX(21); // set the position of the box
  $pdf->SetFont('RobotoCondensed-Bold');
  $pdf->Cell(10,10, 'Not Met', 0, 0, 'L'); // add the text, align to Center of cell
  
  $pdf->SetFont('RobotoCondensed-Regular');
  $pdf->SetTextColor(27,24,17);
  $pdf->SetFontSize('10'); // set font size
  $pdf->Ln(8); // set the position of the box
  $pdf->write(4,$standard_stats['NotMet_questions']);
  $pdf->Ln(4);
  }

  if(!empty($standard_stats['Not_Applicable_questions'])){
  // set the Not Applicable question
  $pdf->SetTextColor(58,186,131);// green color  
  $pdf->SetFontSize('12'); // set font size
  $pdf->SetX(21); // set the position of the box
  $pdf->SetFont('RobotoCondensed-Bold');
  $pdf->Cell(10,10, 'Not applicable', 0, 0, 'L'); // add the text, align to Center of cell
  
  $pdf->SetFont('RobotoCondensed-Regular');
  $pdf->SetTextColor(27,24,17);
  $pdf->SetFontSize('10'); // set font size
  $pdf->Ln(8); // set the position of the box
  $pdf->write(4,$standard_stats['Not_Applicable_questions']);
  $pdf->Ln(4);
  }
  // preparing next page for improvment plan
  $tpl = $pdf->importPage(5);
  $pdf->AddPage();
  $pdf->useTemplate($tpl);

  $pdf->setXY(21,100);
  if(!empty($standard_stats['Improvment'])){
  $pdf->SetFontSize(13);
  $pdf->SetTextColor(58,186,131);
  $pdf->SetFont('RobotoCondensed-Bold');
  $pdf->write(4,"Recommended Performance Improvement Steps");
  $pdf->Ln(6);
  $pdf->SetTextColor(4,13,25);
  $pdf->SetFontSize(10);
  $pdf->SetFont('RobotoCondensed-Regular');
  $pdf->write(5,$standard_stats['Improvment']);}

  if(!empty($standard_stats['capacity_building'])){
  $pdf->Ln(5);
  $pdf->SetFontSize(13);
  $pdf->SetTextColor(58,186,131);
  $pdf->SetFont('RobotoCondensed-Bold');
  $pdf->write(4,"Recommended Capacity Building");
  $pdf->Ln(6);
  $pdf->SetTextColor(4,13,25);
  $pdf->SetFontSize(10);
  $pdf->SetFont('RobotoCondensed-Regular');
  $pdf->write(4,$standard_stats['capacity_building']);
  }
}




// render PDF to browser
//return $pdf->GetPageHeight();

//$pdf->write(5,"2.Does your organization have organizational policies & standard operating procedures related to governance?");
//return $pdf->AcceptPageBreak();
///return ($pdf->GetPageWidth()-21).'  '.$pdf->GetStringWidth("2.Does your organization have organizational policies & standard operating procedures related to governance?");

//$pdf->SetXY(21,122); // set the position of the box


$tpl = $pdf->importPage(7);
$pdf->AddPage();
$pdf->useTemplate($tpl);

$tpl = $pdf->importPage(8);
$pdf->AddPage();
$pdf->useTemplate($tpl);

$pdf->Output();
//$pdf->download('invoice.pdf');
    }

public function addLogos(Request $request)
   {
     for($i=1;$i<=10;$i++){
    $standard = Standard::find($i);
    $standard->update(["logo"=>Storage::put('public/standards_logo', $request->file($i))]);
     }
   }

}
