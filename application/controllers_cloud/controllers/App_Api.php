<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class App_Api extends CI_Controller {

    public function __construct() {
        parent::__construct();

        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Api_Model');
        $this->load->model('Sale_model');
        $this->load->model('Stock_model');
		$this->load->model('Target_model');
    }

    public function login() {
         
        $method = $_SERVER['REQUEST_METHOD'];
       
        if ($method != 'POST') {
            $this->json_output(200, array('status' => true, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $session= $this->input->post('session');
                $response = $this->Api_Model->login($username, $password, $session);
                $this->echoResponse($response);
            }
        }
    }
    
    public function test_gst() {
         echo '{"taxpayerInfo":{"cxdt":"","frequencyType":"MONTHLY","nba":["Retail Business","Office / Sale Office","Warehouse / Depot"],"adadr":[{"addr":{"flno":"","st":"Chinchnaka, Opp.SBI","dst":"Ratnagiri","bno":"Gala No.1","loc":"Chiplun","lt":"","city":"","pncd":"415605","stcd":"Maharashtra","bnm":"Prime Centre","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Karad Road","dst":"Ratnagiri","bno":"Gala No.34","loc":"Chiplun","lt":"","city":"","pncd":"415605","stcd":"Maharashtra","bnm":"Suvarna Mandir Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Main Road,Opp.Modern Bakery","dst":"Kolhapur","bno":"Gala No.1109/1","loc":"Gadhinglaj","lt":"","city":"","pncd":"416502","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Nr.Vyankatrao Highschool, Opp.Dr. Bhide","dst":"Kolhapur","bno":"5/99","loc":"Ichalkaranji","lt":"","city":"","pncd":"416115","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Center One","dst":"Kolhapur","bno":"Plot No.155,Gala No. B 101","loc":"Ichalkaranji","lt":"","city":"","pncd":"416115","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Manakeshwar Talkies,Peth, Sangli Road","dst":"Sangli","bno":"Shop No.2296/172","loc":"Islampur","lt":"","city":"","pncd":"415409","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Kranti Chowk, Near Old Court, Sangli-Kolhapur Road","dst":"Kolhapur","bno":"0","loc":"Jaysingpur","lt":"","city":"","pncd":"416101","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Mudra","dst":"Sindhudurg","bno":"S.No.207 A, H.No.43, Shop No.3","loc":"Kankavali","lt":"","city":"","pncd":"416602","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shaniwar Peth","dst":"Satara","bno":"Plot No.469, B/3","loc":"Karad","lt":"","city":"","pncd":"415110","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shaniwar Peth","dst":"Satara","bno":"37","loc":"Karad","lt":"","city":"","pncd":"415110","stcd":"Maharashtra","bnm":"Indu Complex,C/O.Shyam Sales","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shahupuri","dst":"Kolhapur","bno":"399, E Ward","loc":"Kolhapur","lt":"","city":"","pncd":"416001","stcd":"Maharashtra","bnm":"Ratikamal Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opp.Balgopal Talim Mandal, Mangalwar Peth","dst":"Kolhapur","bno":"Gala  No.16,28,15,29","loc":"Kolhapur","lt":"","city":"","pncd":"416012","stcd":"Maharashtra","bnm":"Mahila Seva Sankul","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"4th Lane, Rajarampuri","dst":"Kolhapur","bno":"2018/Kh/20","loc":"Kolhapur","lt":"","city":"","pncd":"416008","stcd":"Maharashtra","bnm":"Prabhavati Appt","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shivaji Road, Nr.Padma Tawlkies, Bindu Chowk","dst":"Kolhapur","bno":"1555/3, C Ward","loc":"Kolhapur","lt":"","city":"","pncd":"416012","stcd":"Maharashtra","bnm":"Business Point Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Waterfront, Nr.D Mart, Rankala","dst":"Kolhapur","bno":"R.S.No.1234/3, 1316/1, Shop No.16 A, Plot No.1","loc":"Kolhapur","lt":"","city":"","pncd":"416012","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Main Laxmi Market, Nr.Police Station","dst":"Sangli","bno":"C.S.No.5089,B, Gala No.1 , 2","loc":"Miraj","lt":"","city":"","pncd":"416410","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Padma Road, Near Jay Bhawani Path Sanshta","dst":"Kolhapur","bno":"0","loc":"Peth Vadgaon","lt":"","city":"","pncd":"416112","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Maruti Mandir","dst":"Ratnagiri","bno":"Shop No.15, 16","loc":"Ratnagiri","lt":"","city":"","pncd":"415612","stcd":"Maharashtra","bnm":"Navkar Plaza","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"M.G. Road","dst":"Sangli","bno":"B-1, 2","loc":"Sangli","lt":"","city":"","pncd":"416410","stcd":"Maharashtra","bnm":"Shiv Merian Appt","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Bhuvikas Petrol Pump,Sadar Bazar","dst":"Satara","bno":"Shop No.5,6,7, Pl.No.3,Sarvey No.481 A","loc":"Satara","lt":"","city":"","pncd":"415001","stcd":"Maharashtra","bnm":"Pushpdatta Appt","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Sadar Bazar","dst":"Satara","bno":"Gala No.34, 523/A/1/7","loc":"Satara","lt":"","city":"","pncd":"415002","stcd":"Maharashtra","bnm":"Govind Plaza","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Graund Floor","st":"Khanchand Mkt, Goldking Peth","dst":"Solapur","bno":"Shop No.4, ,H.No.97/7","loc":"Solapur","lt":"","city":"","pncd":"416005","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Nr.Hdfc Bank, Karad Road","dst":"Sangli","bno":"428/1/2,","loc":"Vita, Sangli","lt":"","city":"","pncd":"415311","stcd":"Maharashtra","bnm":"Pandurang Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Khanbhag, Kupwad","dst":"Sangli","bno":"C.S.No.404/3, Gala No.12,13,14","loc":"Sangli","lt":"","city":"","pncd":"416410","stcd":"Maharashtra","bnm":"Shiv Meridian","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"GROUND FLOOR","st":"SHOP NO.13","dst":"Pune","bno":"C.S.NO.84/1/2/3A/9/10D/3/4/5","loc":"HADAPSAR, TAL.- HAVELI","lt":"","city":"","pncd":"411028","stcd":"Maharashtra","bnm":"VAIBHAV COMMERCIAL COMPLEX","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground floor","st":"Near Bhuyekar Petrol Pump, Near Bank Of Maharastra","dst":"Kolhapur","bno":"Shop No. 4/5","loc":"Kagal","lt":"","city":"","pncd":"416216","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opposite Akashwani, Professor Chowk. Savedi","dst":"Ahmednagar","bno":"Sai Palace, Shop No. 10-11","loc":"Ahmednagar","lt":"","city":"","pncd":"414003","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"DELHIGATE, GUNDU BAZAR","dst":"Ahmednagar","bno":"SHOP NO.1","loc":"AHMEDNAGAR","lt":"","city":"","pncd":"414001","stcd":"Maharashtra","bnm":"CHANDRALOK APPT.","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Rajaram complex, Sangola","dst":"Solapur","bno":"Shop No. 9","loc":"Solapur","lt":"","city":"","pncd":"413307","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"GROUND FLOOR","st":"OPP.PUSHKAR SANSKRITIK BHAVAN, ISLAMPUR, TAL-WALVA","dst":"Sangli","bno":"R.S.NO.72- 1","loc":"ISLAMPUR","lt":"","city":"","pncd":"415409","stcd":"Maharashtra","bnm":"SHRI RAM KRISHNA HARI BUILDING","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"BHADULE CHOWK","dst":"Solapur","bno":"C.S.NO.4041-A 1, WARD NO.7","loc":"PANDHARPUR","lt":"","city":"","pncd":"413304","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"GAONBHAG","dst":"Sangli","bno":"C.S.NO.1056-4, SHOP NO. 2 AND 3","loc":"SANGLI","lt":"","city":"","pncd":"415410","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"GROUND FLOOR","st":"PATIL TOWERS,GAVALI TITHA, NR.S.T.STAND","dst":"Sindhudurg","bno":"S.N. 85-1- A","loc":"SAWNTWADI","lt":"","city":"","pncd":"416510","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"INDAPUR-PRANALI SHOPPING CENTRE","dst":"Solapur","bno":"PANDHARPUR RD.,CTS NO.2258/3, SHOP NO.3","loc":"AKLUJ","lt":"","city":"","pncd":"413101","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"SADASHIV PETH","dst":"Pune","bno":"SHOP NO.5, SURVEY NO.1164","loc":"PUNE","lt":"","city":"","pncd":"411030","stcd":"Maharashtra","bnm":"GANESH SADAN","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"RAILWAY LINES","dst":"Solapur","bno":"PLOT NO.11-14, SHOP NO. 1 AND 2, CTS NO.840-2-2 F","loc":"SOLAPUR","lt":"","city":"","pncd":"413001","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"GROUND FLOOR","st":"SOMWAR PETH","dst":"Solapur","bno":"C.S.NO.4305","loc":"BARSHI","lt":"","city":"","pncd":"413401","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"GROUND FLOOR","st":"OPP.SBI, BALAJI NAGAR CHOWK, DHANKAWADI","dst":"Pune","bno":"C.S.NO.30-1, PLOT NO.9, SHOP NO.1 AND 2","loc":"PUNE","lt":"","city":"","pncd":"400043","stcd":"Maharashtra","bnm":"GURUPRASAD BUILDING","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"1st Floor","st":"Indapur","dst":"Pune","bno":"Property No.Old 895/78","loc":"Indapur","lt":"","city":"","pncd":"413106","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"Upper Ground Floor","st":"Mudhal Titta","dst":"Kolhapur","bno":"Gat No.183,Shop No.1 and 2","loc":"Kagal","lt":"","city":"","pncd":"416209","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"1st Floor","st":"Vita","dst":"Sangli","bno":"C.S.No.1013,  Shop No. 1 and 2","loc":"Taluka Khanapur","lt":"","city":"","pncd":"415311","stcd":"Maharashtra","bnm":"Lakade Plaza","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"Ground Floor","st":"Athawadi Bazar","dst":"Ratnagiri","bno":"Shop No.36,","loc":"Ratnagiri","lt":"","city":"","pncd":"415605","stcd":"Maharashtra","bnm":"Sankeshwar Arcade","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"Lower Ground Floor","st":"Opp.Railway phatak, Pach Bunglow Area","dst":"Kolhapur","bno":"C.S.No.1115/B/2,Shop.No.5","loc":"Kolhapur","lt":"","city":"","pncd":"416001","stcd":"Maharashtra","bnm":"Tathastu Corner","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"","st":"Near Hutatma Samarak, Opp.State Bank Of India, Main Road","dst":"Satara","bno":"0","loc":"Koregaon","lt":"","city":"","pncd":"415501","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"Ground Floor","st":"A/P. and Tal. - Shirur","dst":"Pune","bno":"C.S.No.160, House No. D3z - 1000062","loc":"Shirur","lt":"","city":"","pncd":"412210","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"","st":"Kawathemahankal","dst":"Sangli","bno":"C.S No./Gat No.1418 /1, Plot No.132 and 133","loc":"Sangli","lt":"","city":"","pncd":"416405","stcd":"Maharashtra","bnm":"Gajanan Plaza","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"Ground Floor","st":"Near Sai Mandir, Sai Chowk","dst":"Pune","bno":"Shop No. 315","loc":"Pimpari-Camp","lt":"","city":"","pncd":"411017","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"","st":"Sangamner","dst":"Ahmednagar","bno":"Survey No.151/139/1, Shop No.1,2,3 and 4","loc":"Sangamner","lt":"","city":"","pncd":"422605","stcd":"Maharashtra","bnm":"Rajpal Cloth Store","lg":""},"ntr":"Office / Sale Office"},{"addr":{"flno":"Ground Floor","st":"Wai","dst":"Satara","bno":"C.S.NO.976/978/17","loc":"Taluka Wai","lt":"","city":"","pncd":"412803","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office, Retail Business"},{"addr":{"flno":"Ground Floor","st":"Kudal","dst":"Sindhudurg","bno":"3519","loc":"Sindhudurg","lt":"","city":"","pncd":"416520","stcd":"Maharashtra","bnm":"Chintamani Plaza","lg":""},"ntr":"Office / Sale Office, Retail Business"},{"addr":{"flno":"","st":"Namoha Compound","dst":"Ahmednagar","bno":"Plot No.93","loc":"Ahmednagar","lt":"","city":"","pncd":"414001","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Office / Sale Office, Retail Business"},{"addr":{"flno":"Floor No. 1,2,3,4","st":"Near Gurunanak petrol pump,Gadmudshingi Occupied Gandhinagar","dst":"Kolhapur","bno":"M. No.1611/f-13","loc":"Kolhapur","lt":"","city":"","pncd":"416122","stcd":"Maharashtra","bnm":"Ahuja Building","lg":""},"ntr":"Warehouse / Depot"},{"addr":{"flno":"Ground Floor","st":"Near Gurunanak petrol pump,Gadmudshingi Occupied Gandhinagar","dst":"Kolhapur","bno":"M. No.1611/f-13","loc":"Kolhapur","lt":"","city":"","pncd":"416122","stcd":"Maharashtra","bnm":"Ahuja Building","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shivaji Nagar","dst":"Latur","bno":"Gala No. 8,9,10,15,16,17","loc":"Latur","lt":"","city":"","pncd":"413512","stcd":"Maharashtra","bnm":"Madhu-Mira Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"South Kasaba Peth","dst":"Solapur","bno":"C.S.NO.744/A, House No. 697, Shop No. G-4","loc":"Solapur","lt":"","city":"","pncd":"413007","stcd":"Maharashtra","bnm":"Salgar Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Near Bhagyarekha Talkies, Main Road","dst":"Kolhapur","bno":"Ward No.16, Prop.No.162002035, Old Prop.No.16/1537","loc":"Ichalkaranji","lt":"","city":"","pncd":"416115","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Upper Ground Floor","st":"Miraj Highschool Road","dst":"Kolhapur","bno":"C.S.NO.5875 G, Shop No.19 and 20","loc":"Miraj","lt":"","city":"","pncd":"416410","stcd":"Maharashtra","bnm":"Miraj Highschool Building","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opp.Idbi Bank, Powai Naka","dst":"Satara","bno":"Shop No. 1a","loc":"Satara","lt":"","city":"","pncd":"415001","stcd":"Maharashtra","bnm":"Vitthal Leela Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"DEd Chowk, Ring Road , Laxmi Nagar","dst":"Satara","bno":"0","loc":"Phaltan","lt":"","city":"","pncd":"415015","stcd":"Maharashtra","bnm":"Sai Plaza","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"J.M.Road","dst":"Pune","bno":"CTS NO.418, Shop No.3/4/5","loc":"Pune","lt":"","city":"","pncd":"411004","stcd":"Maharashtra","bnm":"Mittal Chambers","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Opp.Maruti Mandir","dst":"Ratnagiri","bno":"S.N.372, B, C.S.No.175/B, Shop No.3","loc":"Ratnagiri","lt":"","city":"","pncd":"415612","stcd":"Maharashtra","bnm":"Samrat Shopping Center","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Manchar","dst":"Pune","bno":"Shop No.1","loc":"Taluka Ambegaon","lt":"","city":"","pncd":"410503","stcd":"Maharashtra","bnm":"Vitthal Smuti Building","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opposite Sudhir Gas Agency, Jod Basvanna Chowk, Shakhar Peth","dst":"Solapur","bno":"Shop No.2 and 6","loc":"Solapur","lt":"","city":"","pncd":"413005","stcd":"Maharashtra","bnm":"Degaonkar Sankul","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Gandhi Market, Chainsukh Road","dst":"Latur","bno":"Shop No.1","loc":"Latur","lt":"","city":"","pncd":"413512","stcd":"Maharashtra","bnm":"Kedar Mobile","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor and Half Part Of 2nd Shop","st":"Shukrawar Peth, Rashtrabhushan Chowk","dst":"Pune","bno":"C.S.No.961","loc":"Swargate","lt":"","city":"","pncd":"411002","stcd":"Maharashtra","bnm":"Thorat Building","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Jayhind Chowk, Mangalwar Peth, Near State Bank Of India","dst":"Sangli","bno":"0","loc":"Jath","lt":"","city":"","pncd":"416404","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shivaji Nagar,Nea Lakshmidevi Girls High School, Rendal Road","dst":"Kolhapur","bno":"Milkat No.348/B","loc":"Hupari","lt":"","city":"","pncd":"416203","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Upper Ground Floor","st":"Shivaji Road","dst":"Ahmednagar","bno":"C.S.No.517,Plot No.799,Shop No.2 and 3","loc":"Shrirampur","lt":"","city":"","pncd":"413709","stcd":"Maharashtra","bnm":"Kunal Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Karad-Tasgaon Road, Near Old Bus Stand,Near Mansing Bank","dst":"Sangli","bno":"0","loc":"Palus","lt":"","city":"","pncd":"416310","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Shaniwar Peth, Opp. ST Stand","dst":"Satara","bno":"Shop No 6, 7 and 8, Survay No.34/13 and 14","loc":"Karad","lt":"","city":"","pncd":"415110","stcd":"Maharashtra","bnm":"Krishna Krupa","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"DP Road","dst":"Sindhudurg","bno":"Shop No. 2 and 3","loc":"Kankavali","lt":"","city":"","pncd":"416602","stcd":"Maharashtra","bnm":"Bandu Harne Building","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Guruwar Peth, Near Ganpati Temple","dst":"Sangli","bno":"0","loc":"Tasgaon","lt":"","city":"","pncd":"416312","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Chatrapati Shahaji Stadium, E Ward","dst":"Kolhapur","bno":"C.S.NO.1090, Shop Unit No.27 and 28","loc":"Shahupuri","lt":"","city":"","pncd":"416001","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"A/P - Vaduj, above Mongenes, Shivaji Chowk","dst":"Satara","bno":"0","loc":"Vaduj","lt":"","city":"","pncd":"415506","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Collage Road","dst":"Nashik","bno":"Shop No 1","loc":"Near Vision Hospital","lt":"","city":"","pncd":"422005","stcd":"Maharashtra","bnm":"Vasant chhaya","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Shilpa Hotel, Opp. Yashwant Vyayam Shala","dst":"Nashik","bno":"Shop No 3/4","loc":"MG Road","lt":"","city":"","pncd":"422001","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opp Vijay sale, near silver park signal","dst":"Thane","bno":"Shop no -11,12","loc":"Mira road east","lt":"","city":"","pncd":"401107","stcd":"Maharashtra","bnm":"Ashadeep building","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"opposite mira tower, new link road","dst":"Mumbai City","bno":"Shop no 5 and 6 building no.26","loc":"Andheri west","lt":"","city":"","pncd":"400053","stcd":"Maharashtra","bnm":"mhada residential complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Opp Jogeshwari Railway station","dst":"Mumbai City","bno":"Shop No.12","loc":"Jogeshwari West","lt":"","city":"","pncd":"400102","stcd":"Maharashtra","bnm":"Abba Residency","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opp Thakur Collage , Thakur Village","dst":"Mumbai City","bno":"Shop No. 7 and 8","loc":"Kandivali East","lt":"","city":"","pncd":"400101","stcd":"Maharashtra","bnm":"Shree Ganesh Angan Society","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Charkop","dst":"Mumbai City","bno":"Shop No.38 and 44","loc":"Kandivali West","lt":"","city":"","pncd":"400067","stcd":"Maharashtra","bnm":"Kesar Residency C.H.S Ltd","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opp. Om Hospital, Shiram Colony, Alandi Road","dst":"Pune","bno":"A7O","loc":"Bhosari","lt":"","city":"","pncd":"411039","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Behind City Center Mall,  near Kankariyas Jwellers","dst":"Nashik","bno":"Shop No.1","loc":"Trimurti Chowk","lt":"","city":"","pncd":"422009","stcd":"Maharashtra","bnm":"Vraj Bhoomi Apartment","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"S.V Road","dst":"Mumbai City","bno":"CTS No.538,Shop No.5 and 6","loc":"Goregaon West","lt":"","city":"","pncd":"400062","stcd":"Maharashtra","bnm":"Hiren Shopping Centre","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Opp Jogeshwari Railway Station","dst":"Mumbai City","bno":"Shop No.3","loc":"Jogeshwari West","lt":"","city":"","pncd":"400102","stcd":"Maharashtra","bnm":"Habib Park Co Op Society Ltd","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Near Railway Reservation Centre, Anand Road","dst":"Mumbai City","bno":"Shop No.5","loc":"Malad West","lt":"","city":"","pncd":"400064","stcd":"Maharashtra","bnm":"Parasrampuria Chambers","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Station Road, Opposite Bassein Catholic Bank","dst":"Mumbai City","bno":"Shop No. 01","loc":"Pandit Dindayal Nagar,Vasai West","lt":"","city":"","pncd":"401202","stcd":"Maharashtra","bnm":"Mukesh Apartment","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Gaothan","dst":"Thane","bno":"Shop No.3 and 4","loc":"Virar West","lt":"","city":"","pncd":"401303","stcd":"Maharashtra","bnm":"Gauri Bhavan, Parijat Co-op Hsg Society","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"150 Ft. Road, Near Maxus Mall","dst":"Thane","bno":"9 and 10","loc":"Bhayander West","lt":"","city":"","pncd":"401101","stcd":"Maharashtra","bnm":"Saroj Plaza","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Ram Mandir Road","dst":"Mumbai Suburban","bno":"Shop No.1 and 2","loc":"Goregaon West","lt":"","city":"","pncd":"400067","stcd":"Maharashtra","bnm":"Sairam Apartments","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"2nd Floor","st":"Village Pahadi","dst":"Mumbai Suburban","bno":"CTS No.1 A-58, Shop No.14","loc":"Goregaon west","lt":"","city":"","pncd":"400104","stcd":"Maharashtra","bnm":"Harmony Mall","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Indira Peth, Navi Peth, Collage Road","dst":"Ahmednagar","bno":"2228, Shop No.6","loc":"Opp.Bhagirathi Kanya Vidyalaya, Rahuri","lt":"","city":"","pncd":"413705","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Opp. Khed Police Station","dst":"Ratnagiri","bno":"Shop No.2,3,4","loc":"Khed","lt":"","city":"","pncd":"415709","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Near Tulajapur S.T.Stand, Osmanabad Road","dst":"Osmanabad","bno":"C.S.No.2474/1, Shop No.1","loc":"Tulajapur","lt":"","city":"","pncd":"413601","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Karanje Chowk, Near Bagwan Masjid","dst":"Solapur","bno":"C.S.No.739","loc":"Akkalkot","lt":"","city":"","pncd":"413001","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"","st":"Village Manjeri Bibewewadi","dst":"Pune","bno":"Office Premise No.360","loc":"Lullanagar","lt":"","city":"","pncd":"411040","stcd":"Maharashtra","bnm":"Marvel Vista Building, Sahaney Sujan Park","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"3rd Floor","st":"South Kasaba Peth","dst":"Solapur","bno":"C.S.NO.744/A, House No.697","loc":"Solapur","lt":"","city":"","pncd":"413007","stcd":"Maharashtra","bnm":"Salgar Complex","lg":""},"ntr":"Retail Business"},{"addr":{"flno":"Ground Floor","st":"Bazarpeth","dst":"Ratnagiri","bno":"House No.760 A 1, Shop No.1","loc":"A/p.Kherdi, Taluka-Chiplun","lt":"","city":"","pncd":"415604","stcd":"Maharashtra","bnm":"","lg":""},"ntr":"Retail Business"}],"ctjCd":"UE0301","stj":"KAGAL_501","lgnm":"SS COMMUNICATION & SERVICES PRIVATE LIMITED","rgdt":"01/07/2017","gstin":"27AAXCS2330R1ZH","ctj":"RANGE-I","tradeNam":"M/s SS COMMUNICATION & SERVICES PRIVATE LTD.","ctb":"Private Limited Company","sts":"Active","pradr":{"addr":{"flno":"","st":"Basant Bahar Road","dst":"Kolhapur","bno":"399/B, E Ward","loc":"Shahupuri","lt":"","city":"","pncd":"416003","stcd":"Maharashtra","bnm":"Ratikamal Chambers","lg":""},"ntr":"Retail Business"},"errorMsg":null,"dty":"Regular","stjCd":"MHCG0057","panNo":"AAXCS2330R"}, "filing":[], "compliance": {"filingFrequency":null}}';
//         echo '{"error":true,"message":"INVALID GSTN/UID","help":"Contact Team Appyflow at +91 85689 93655"}';
//           echo '[{"Message":"' . $id . ' Number of pincode(s) found:13","Status":"Success","PostOffice":[{"Name":"Are","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Bachani","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Ghanavade","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Radhanagari","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Haldi","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Hasur Dumala","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Kolhapur RS","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karvir","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Kothali","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Kurukali","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveern","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Malyachi Shiroli","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karvir","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Mhalsavade","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Radhanagari","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Sadoli Khalsa","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Savarde Dumala","Description":null,"BranchType":"Branch Post Office","DeliveryStatus":"Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karveer","State":"Maharashtra","Country":"India","Pincode":"416001"},{"Name":"Shahupuri","Description":null,"BranchType":"Sub Post Office","DeliveryStatus":"Non-Delivery","Circle":"Maharashtra","District":"Kolhapur","Division":"Kolhapur","Region":"Goa-Panaji","Block":"Karvir","State":"Maharashtra","Country":"India","Pincode":"416001"}]}]';
           echo '{"ResponseCode":"00","ResponseMessage":"success","DoDetails":{"NetLoanAmount":36000.0,"OtherCharges":0.0,"SFDCLTV":90.0,"CoBrandCardCharges":0.0,"CustomerDownPayment":4000.0,"Subvention":1005.0,"MarginMoney":0.0,"NetDisbursement":34695.0,"MFRSubvention":4.12,"BFLShare":0.0,"ProcessingFees":0.0,"SpecialCharges":0.0,"EMICardFee":0.0,"GrossLoanAmount":40000.0,"EMICardLimit":0.0,"DONumber":"B41311588","AdvanceEMI":"4000","CreatedOn":"2021-05-20 11:58:51","SchemeId":"5008842","Status":"New","DUEDAY":2,"CustomerName":"Maggie null","DealID":"A21755840","CobrandCardLimit":null,"Tenure":"10","MAKE":"Samsung Mobile","DealerCode":"510184","CustomerPhoneNo":"8790901242","DMEID":"273031","SerialNo":null,"InvoiceAmount":"40000","ParentID":null,"Field1":"","Field2":"","Field3":null,"Field4":"0","Field5":null,"Field6":null,"Field7":"8765456345","Field8":null,"Field9":"0.0","Field10":"true","Field11":"0.0","Field12":"0.0","Field13":"","Field14":"","Field15":"","VAN":null,"CustomerPAN":null,"PinCode":"411014","CITY":"PUNE","STATE":"Maharashtra","customerGSTIN":null,"InvoiceExpiryDate":"20 May, 2049","assetCategory":"PHONE(WEB-MOBILE)","ManufacturerName":"SAMSUNG","DealerName":"testpartner","CustomerFirstName":"Maggie","CustomerMiddleName":null,"CustomerLastName":null,"CustomerEmailID":null,"AddressLine1":"asfghasjvfh","AddressLine2":null,"AddressLine3":null,"Area":null,"Landmark":null,"CdLine":"0","AppliancesLine":"0","DigitalLine":"0","AddOnCardRequested":null,"AddOnCardCharges":"0","InstaCardActivationFees":"0","ModelNo":"SM-G975FCWGINS - Samsung Mobile Rs48000","ImpsCharges":null,"NetTenure":null,"SubventionPercentage":"2.13","TotalGSTOnDBD":null,"RiskPoolAmount":"0","TotalDeductions":"1305","ProductEMI":"4000","TotalEMI":"4000","PromoPayableByBFLPrcent":null,"PromoPayableByRetailerPrcent":null,"PromoPayableByManufacturerPrcent":null,"PromoPayableByBFLValue":null,"PromoPayableByRetailerValue":null,"PromoPayableByManufacturerValue":null,"TotalPromoValue":null,"UpfrontInterest":"0","ServiceCharge":"0","TotalPromoPrcent":null}}';
       
    }
    public function test_bfl() {
         echo '{"ResponseCode":"00","ResponseMessage":"success","DoDetails":{"NetLoanAmount":36000.0,"OtherCharges":0.0,"SFDCLTV":90.0,"CoBrandCardCharges":0.0,"CustomerDownPayment":4000.0,"Subvention":1005.0,"MarginMoney":0.0,"NetDisbursement":34695.0,"MFRSubvention":4.12,"BFLShare":0.0,"ProcessingFees":0.0,"SpecialCharges":0.0,"EMICardFee":0.0,"GrossLoanAmount":40000.0,"EMICardLimit":0.0,"DONumber":"B41311588","AdvanceEMI":"4000","CreatedOn":"2021-05-20 11:58:51","SchemeId":"5008842","Status":"New","DUEDAY":2,"CustomerName":"Maggie null","DealID":"A21755840","CobrandCardLimit":null,"Tenure":"10","MAKE":"Samsung Mobile","DealerCode":"510184","CustomerPhoneNo":"8790901242","DMEID":"273031","SerialNo":null,"InvoiceAmount":"40000","ParentID":null,"Field1":"","Field2":"","Field3":null,"Field4":"0","Field5":null,"Field6":null,"Field7":"8765456345","Field8":null,"Field9":"0.0","Field10":"true","Field11":"0.0","Field12":"0.0","Field13":"","Field14":"","Field15":"","VAN":null,"CustomerPAN":null,"PinCode":"411014","CITY":"PUNE","STATE":"Maharashtra","customerGSTIN":null,"InvoiceExpiryDate":"20 May, 2049","assetCategory":"PHONE(WEB-MOBILE)","ManufacturerName":"SAMSUNG","DealerName":"testpartner","CustomerFirstName":"Maggie","CustomerMiddleName":null,"CustomerLastName":null,"CustomerEmailID":null,"AddressLine1":"asfghasjvfh","AddressLine2":null,"AddressLine3":null,"Area":null,"Landmark":null,"CdLine":"0","AppliancesLine":"0","DigitalLine":"0","AddOnCardRequested":null,"AddOnCardCharges":"0","InstaCardActivationFees":"0","ModelNo":"SM-G975FCWGINS - Samsung Mobile Rs48000","ImpsCharges":null,"NetTenure":null,"SubventionPercentage":"2.13","TotalGSTOnDBD":null,"RiskPoolAmount":"0","TotalDeductions":"1305","ProductEMI":"4000","TotalEMI":"4000","PromoPayableByBFLPrcent":null,"PromoPayableByRetailerPrcent":null,"PromoPayableByManufacturerPrcent":null,"PromoPayableByBFLValue":null,"PromoPayableByRetailerValue":null,"PromoPayableByManufacturerValue":null,"TotalPromoValue":null,"UpfrontInterest":"0","ServiceCharge":"0","TotalPromoPrcent":null}}';
       
    }

    public function app_version() {       
            $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Api_Model->get_app_version();
				$this->echoResponse($response);
                
            }
    }
    public function logout() {       
            $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Api_Model->logout();
                die($response);
            }
    }

    public function verify_customer() {
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $cust_contact = $this->input->post('mob_number');
                        $response = $this->Api_Model->get_customer_bycontact($cust_contact);
//                        $this->json_output(200,$response);
                        $this->echoResponse($response);
                    }
            }
    }
    
     public function get_payment_data() {
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();                    
                    if($auth['status'] == 200){
                           $result=array(); 
                            $data['payment_head']=array();
                            $head_data = $this->General_model->get_active_payment_head();
                            foreach ($head_data as $head){
                                $tmp=array();
                                 foreach ($head as $key => $value) {
                                    $tmp[$key] = $value;
                                }                                
                                $tmppayment_mode = $this->General_model->get_active_billing_payment_mode_byhead($head->id_paymenthead);
                                $tmppayment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head->id_paymenthead);
                                $tmp['payment_mode'] = json_decode(json_encode($tmppayment_mode), true);
                                $tmp['payment_attribute'] = json_decode(json_encode($tmppayment_attribute), true);
                                array_push($data['payment_head'], $tmp);                                
                            }                            
                            $result['status']=200;
                            $result['message']='Payment Mode Details Available';
                            $result['data']=$data;
//                            die(print_r($result));
                           echo  json_encode($result);
                    }
            }
    }

    public function register_customer() {
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $datetime = date('Y-m-d H:i:s');
                        $state_name = $this->input->post('customer_state');
                        $customer_gst = $this->input->post('customer_gst');
                        $state_data = $this->Sale_model->get_state_bystate_name($state_name);

                        $latitude = 0;
                        $longitude = 0;
                        $data = array(
                            'customer_fname' => $this->input->post('customer_fname'),
                            'customer_lname' => $this->input->post('customer_lname'),
                            'customer_contact' => $this->input->post('customer_contact'),
                            'customer_gst' => $this->input->post('customer_gst'),
                            'customer_pincode' => $this->input->post('customer_pincode'),
                            'customer_city' => $this->input->post('customer_city'),
                            'customer_district' => $this->input->post('customer_district'),
                            'idstate' => $state_data->id_state,
                            'customer_state' => $state_name,
                            'customer_address' => $this->input->post('customer_address'),
                            'idbranch' => $this->input->post('idbranch'),
                            'created_by' => $this->input->post('iduser'),
                            'customer_latitude' => $latitude,
                            'customer_longitude' => $longitude,
                            'entry_time' => $datetime,
                        );

                        $idcustomer = $this->Sale_model->save_customer($data);
                        if ($idcustomer) {
                            $q['customer'] = $this->Sale_model->get_customer_byid($idcustomer);
                            if (count($q['customer'])) {
                //                    $this->json_output(200,array('status' => 'true','message' => 'Customer registrred successfully!', 'data' => $q ));
                                $this->echoResponse(array('status' => 200, 'message' => 'Customer registrred successfully!', 'data' => $q));
                            } else {
                                $this->echoResponse(array('status' => 204, 'message' => 'Fail to register customer.. Try again!.', 'data' => $q));
                            }
                        } else {
                            $this->echoResponse(array('status' => 204, 'message' => 'Fail to register customer.. Try again!.', 'data' => $q));
                        }
                    }
            }
    }
    
    
    
      public function update_customer() {
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $this->db->trans_begin();
                        $datetime = date('Y-m-d H:i:s');
                        
                        $idcustomer = $this->input->post('idcustomer');
                        $state_name = $this->input->post('customer_state');
                        $customer_gst = $this->input->post('customer_gst');
                        $state_data = $this->Sale_model->get_state_bystate_name($state_name);

                        $latitude = 0;
                        $longitude = 0;
                        $data = array(
                            'customer_fname' => $this->input->post('customer_fname'),
                            'customer_lname' => $this->input->post('customer_lname'),
                            'customer_contact' => $this->input->post('customer_contact'),
                            'customer_gst' => $this->input->post('customer_gst'),
                            'customer_pincode' => $this->input->post('customer_pincode'),
                            'customer_city' => $this->input->post('customer_city'),
                            'customer_district' => $this->input->post('customer_district'),
                            'idstate' => $state_data->id_state,
                            'customer_state' => $state_name,
                            'customer_address' => $this->input->post('customer_address'),
                        );

                        $this->General_model->edit_customer_byid($idcustomer, $data);
                        $customer_history = array(
                            'customer_fname' => $this->input->post('customer_fname'),
                            'customer_lname' => $this->input->post('customer_lname'),
                            'customer_address' => $this->input->post('customer_address'),
                            'idcustomer' => $idcustomer,
                            'customer_gst' => $this->input->post('customer_gst'),
                            'customer_pincode' => $this->input->post('customer_pincode'),
                            'customer_idstate' => $state_data->id_state,                
                            'edited_by' => $this->input->post('iduser'),
                            'entry_time' => date('Y-m-d H:i:s'),
                        );
                        $this->General_model->save_customer_edit_history($customer_history);
                        
                        if ($this->db->trans_status() === FALSE){
                            $this->db->trans_rollback();
                            $q['status'] = 204;
                            $q['message'] = 'Fail to Update customer!';
                        }else{
                            $this->db->trans_commit();
                            $q['status'] = 200;
                            $q['message'] = "Customer Updated successfully!";
                        }
                        echo json_encode($q);
                       
                    }
            }
    }
    
    public function get_qty_model_variant(){
         $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();                        
                        $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);  
                        $q['target']=array();
                        $iduser = $this->input->post('iduser');
                        $idbranch = $this->input->post('idbranch');
                        $date=date('Y-m-d');
                        $q['target'] = $this->Api_Model->get_current_promoter_target($iduser,$idbranch,$date);
                        if (count($q['model_variant'])) {
                            $result['status']=200;
                            $result['message']='Models found';
                            $result['data']=$q;
                            echo json_encode($result);            
                        } else {
                            $result['status']=204;
                            $result['message']='Models not available';
                            $result['data']='';
                            echo json_encode($result);
                        }
                    }
            }
    }
    public function get_states(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();
                        $q['state_data'] = $this->General_model->get_state_data();            
                        if (count($q['state_data'])) {
                            $result['status']=200;
                            $result['message']='State data found';
                            $result['data']=$q;
                            echo json_encode($result);            
                        } else {
                            $result['status']=204;
                            $result['message']='State data not available';
                            $result['data']='';
                            echo json_encode($result);
                        }
                    }
            }
    }
     
    public function ajax_get_imei_details() {
        $check_auth_client = $this->Api_Model->check_auth_client();
        if ($check_auth_client == true) {
            $auth = $this->Api_Model->auth();
            if ($auth['status'] == 200) {
                $imei = $this->input->post('imei');
                $idbranch = $this->input->post('idbranch');
                $skuvariant = $this->input->post('skuvariant');
                $idgodown = $this->input->post('idgodown');
                $models = array();
                if ($skuvariant) {
                    // Quantity
                    $sale_type = $this->input->post('sale_type');
                    if($sale_type=='2'){
                        $models_stock = $this->Api_Model->ajax_get_variant_for_saletype_2($skuvariant, $idbranch, $idgodown);
                        
                        if($models_stock[0]->id_stock==null){
                            $inward_stock_sku = array(
                                'date' => date('Y-m-d'),
                                'idgodown' => $idgodown,
                                'product_name' => $models_stock[0]->full_name,
                                'idskutype' => $models_stock[0]->idsku_type,
                                'idproductcategory' => $models_stock[0]->idproductcategory,
                                'idcategory' => $models_stock[0]->idcategory,
                                'is_gst'   => 1,
                                'idvariant' => $skuvariant,
                                'idbranch' => $idbranch,
                                'idmodel' => $models_stock[0]->idmodel,
                                'idbrand' => $models_stock[0]->idbrand,
                                'qty' => 0,
                            );
                            $this->Api_Model->save_stock($inward_stock_sku);
                            $models['stock'] = $this->Api_Model->ajax_get_variant_byid_branch_godown($skuvariant, $idbranch, $idgodown);
                        }else{
                            $models['stock'] = $models_stock;
                        }
                        $result['status'] = 200;
                        $result['message'] = 'Stock available!!';
                        $result['data'] = $models;
                         
                    }else{
                        $token_data = $this->Api_Model->ajax_token_byid_branch_godown($skuvariant, $idbranch, $idgodown);
                        $models_stock = $this->Api_Model->ajax_get_variant_byid_branch_godown($skuvariant, $idbranch, $idgodown);
                        $qty = (($models_stock[0]->qty) - ($token_data[0]->token_qty));
                        if ($qty > 0) {
                            $models_stock[0]->qty = $qty;
                            $models_stock[0]->ageing = 0;
                            $models_stock[0]->focus_status = 0;
                            $models_stock[0]->focus_amount = 0;
                            $models['stock'] = $models_stock;
                            $result['status'] = 200;
                            $result['message'] = 'Stock available!!';
                            $result['data'] = $models;
                        } else {
                            $result['status'] = 204;
                            $models['stock']=array();
                            $result['message'] = 'Stock not available!!';
                            $result['data'] = $models;
                        }
                    }
                    echo json_encode($result);
                } else {
                    // IMEI/ SRNO
                    $token_data = $this->Api_Model->ajax_token_byimei_branch($imei, $idbranch);
                    if (count($token_data) > 0) {
                        $result['status'] = 204;
                        $result['message'] = 'Sale token is already created for this IMEI/SrNo!';
                        echo json_encode($result);
                    } else {
                        $models['stock'] = $this->Sale_model->ajax_stock_data_byimei_branch($imei, $idbranch);
                        if (count($models['stock'])) {
                            $model = $models['stock'][0];
                            $ageing_data = $this->Stock_model->get_ageing_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                            if ($ageing_data) {
                                $models['stock'][0]->ageing = 1;
                            } else {
                                $models['stock'][0]->ageing = 0;
                            }
                            $focus_data = $this->Stock_model->get_focus_stock_data($model->idproductcategory, $model->idbrand, $model->idmodel, $model->id_variant, $model->idbranch);
                            if ($focus_data) {
                                $models['stock'][0]->focus_status = 1;
                                $models['stock'][0]->focus_amount = $focus_data->incentive_amount;
                            } else {
                                $models['stock'][0]->focus_status = 0;
                                $models['stock'][0]->focus_amount = 0;
                            }
                            $result['status'] = 200;
                            $result['message'] = 'Product available';
                            $result['data'] = $models;
                            echo json_encode($result);
                        } else {
                            $result['status'] = 204;
                            $result['message'] = 'Product not found in branch!';
                            $result['data'] = $models;
                            echo json_encode($result);
                        }
                    }
                }
            }
        }
    }

    public function save_sale_token() {
        $check_auth_client = $this->Api_Model->check_auth_client();
        if ($check_auth_client == true) {
            $auth = $this->Api_Model->auth();
            if ($auth['status'] == 200) {                     
                $idbranch = $this->input->post('idbranch');
                $productData = $this->input->post('product_data');
                $product_data = json_decode($productData);
                $token_uid=$product_data[0]->token_uid;
                $tokenuid = $this->Api_Model->find_sale_token_by_tokenuid($token_uid);
                if(count($tokenuid)>0){
                     $result['status'] = 200;
                    $result['message'] = 'Sale token already generated';
                    $result['data'] = array("saletoken" => $tokenuid[0]->id_sale_token);
                }else{
                $this->db->trans_begin(); 
                
                $paymentData = $this->input->post('payment_data');
                $payment_data = json_decode($paymentData);

                $customeData = $this->input->post('customer_data');
                $customer_data = json_decode($customeData);

                $to_Tals = $this->input->post('totals');
                $totals = json_decode($to_Tals);

                
                $date = date('Y-m-d');
                $datetime = date('Y-m-d H:i:s');
                $idstate = $this->input->post('idstate');
                $idcustomer = $customer_data->id_customer;
                $cust_idstate = $customer_data->idstate;
                $gst_type = 0; //cgst
                if ($idstate != $cust_idstate) {
                    $gst_type = 1; //igst
                }
                $data = array(
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'idsalesperson' => $this->input->post('iduser'),
                    'basic_total' => $totals->total_basic,
                    'discount_total' => $totals->total_discount,
                    'final_total' => $totals->totalamount,
                    'gst_type' => $gst_type,
                    'idsalesperson' => $this->input->post('iduser'),
                    'created_by' => $this->input->post('iduser'),
                    'entry_time' => $datetime,
                    'dcprint' => $product_data[0]->dcprint,
                    'token_uid' => $token_uid,
                );
                $idsaletoken = $this->Api_Model->save_sale_token($data);
//              Payment                
//              die('<pre>'.print_r($payment_data,1).'</pre>');
                foreach ($payment_data as $pay_data) {
                    $transaction_id = $pay_data->transaction_id;
                    if ($pay_data->idpayment_head == 1) {
                        $transaction_id = "";
                    }
                    $payment = array(
                        'date' => $date,
                        'idcustomer' => $idcustomer,
                        'idsaletoken' => $idsaletoken,
                        'idbranch' => $idbranch,
                        'idcustomer' => $idcustomer,
                        'idbranch' => $idbranch,
                        'entry_time' => $datetime,
                        'idpayment_head' => $pay_data->idpayment_head,
                        'idpayment_mode' => $pay_data->idpayment_mode,
                        'amount' => $pay_data->amount,
                        'created_by' => $this->input->post('iduser'),
                        'transaction_id' =>$transaction_id,
                    );
                    foreach ($pay_data as $key => $value) {
                        if ($key == 'attribute_data') {
                            if ($value != null) {
                                $data = json_decode($value);
                                foreach ($data[0] as $keyy => $valuee) {
                                    $payment[$keyy] = $valuee;
                                }
                            }
                        }
                    }
                    $id_sale_payment = $this->Api_Model->save_sale_token_payment($payment);
                }

//                Sale_product

                foreach ($product_data as $product) {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = 0;
                    if ($gst_type == 1) {
                        $igst = $product->igst;
                    } else {
                        $cgst = $product->cgst;
                        $sgst = $product->sgst;
                    }
                    $sale_product = array(
                        'date' => $date,
                        'idsaletoken' => $idsaletoken,
                        'idmodel' => $product->idmodel,
                        'idvariant' => $product->idvariant,
                        'imei_no' => $product->imei_no,
                        'hsn' => $product->hsn,
                        'idskutype' => $product->idsku_type,
                        'idgodown' => $product->idgodown,
                        'idproductcategory' => $product->idproductcategory,
                        'idcategory' => $product->idcategory,
                        'idbrand' => $product->idbrand,
                        'product_name' => $product->full_name,
                        'price' => $product->price,
                        'landing' => $product->landing,
                        'mrp' => $product->mrp,
                        'mop' => $product->mop,
                        'nlc_price' => $product->nlc_price,
                        'ageing' => $product->ageing,
                        'focus' => $product->focus_status,
                        'focus_incentive' => $product->focus_amount,
                        'salesman_price' => $product->salesman_price,
                        'qty' => $product->qty,
                        'idbranch' => $product->idbranch,
                        'discount_amt' => $product->discount,
                        'is_gst' => $product->is_gst,
                        'is_mop' => $product->is_mop,
                        'basic' => $product->basic,
                        'idvendor' => $product->idvendor,
                        'cgst_per' => $product->cgst,
                        'sgst_per' => $product->sgst,
                        'igst_per' => $product->igst,
                        'total_amount' => $product->sold_amount,
                        'entry_time' => $datetime,
                        'sale_type' =>$product->sale_type,
                        'idstock' =>$product->id_stock,
                        'insurance_imei_no' => $product->proplan_imei,
                        'activation_code' => $product->proplan_activationcode,
                    );
                    $idsaleproduct = $this->Api_Model->save_sale_token_product($sale_product);
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = 204;
                    $result['message'] = 'Fail to generate Token';
                } else {
                    $this->db->trans_commit();
                    $result['status'] = 200;
                    $result['message'] = 'Sale token generated successfully';
                    $result['data'] = array("saletoken" => $idsaletoken);
                }               
            }
             echo json_encode($result);
            }
        }
    }

    public function save_sale() {
        $check_auth_client = $this->Api_Model->check_auth_client();
        if ($check_auth_client == true) {
            $auth = $this->Api_Model->auth();
            if ($auth['status'] == 200) {    
                $idbranch = $this->input->post('idbranch');
                $productData = $this->input->post('product_data');
                $product_data = json_decode($productData);
                $token_uid=$product_data[0]->token_uid;
                $tokenuid = $this->Api_Model->find_sale_by_tokenuid($token_uid);
                if(count($tokenuid)>0){
                    $result['status'] = 200;
                    $result['message'] = 'Invoice already generated';
                    $result['data'] = array("invoice_no" => $tokenuid[0]->inv_no, "idsale" => $tokenuid[0]->id_sale, "pdf" => 1);                    
                }else{
                $this->db->trans_begin();
                
                $idbranch = $this->input->post('idbranch');
                $paymentData = $this->input->post('payment_data');
                $payment_data = json_decode($paymentData);

                

                $customeData = $this->input->post('customer_data');
                $customer_data = json_decode($customeData);

                $to_Tals = $this->input->post('totals');
                $totals = json_decode($to_Tals);
                
                $iduser = $this->input->post('iduser');
                $dcprint = $product_data[0]->dcprint;
                $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);                
                $invid = $invoice_no->invoice_no + 1; 
                $y = date('y', mktime(0, 0, 0, 9 + date('m')));
                $y1 = $y - 1;
                if($dcprint == 0){
                    $inv_no = $y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);
                }else{
                    $inv_no = 'DC'.$y1 .'-'. $y . '/'. $invoice_no->branch_code . '/' . sprintf('%04d', $invid);
                }        
                $date = date('Y-m-d');
                $datetime = date('Y-m-d H:i:s');
                $idstate = $this->input->post('idstate');
                $idcustomer = $customer_data->id_customer;
                $cust_idstate = $customer_data->idstate;
                $cust_fname = $customer_data->customer_fname;
                $cust_lname = $customer_data->customer_lname;                
                $cust_pincode = $customer_data->customer_pincode;
                $gst_type = 0; //cgst
                if($idstate != $cust_idstate){
                    $gst_type = 1; //igst
                }
                $data = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'customer_fname' => $cust_fname,
                    'customer_lname' => $cust_lname,
                    'customer_idstate' => $cust_idstate,
                    'customer_pincode' => $cust_pincode,
                    'customer_contact' => $customer_data->customer_contact,
                    'customer_address' => $customer_data->customer_address,
                    'customer_gst' => $customer_data->customer_gst,
                    'idsalesperson' => $iduser,
                    'basic_total' => $totals->total_basic,
                    'discount_total' => $totals->total_discount,
                    'final_total' => $totals->totalamount,                    
                    'gst_type' => $gst_type,
                    'created_by' => $iduser,
                    'remark' => $this->input->post('remark'),
                    'entry_time' => $datetime,
                    'dcprint' => $dcprint,
                    'token_uid' => $token_uid,
                    'idsaletoken' => 0,
                );
                $idsale = $this->Sale_model->save_sale($data);
                
                foreach ($payment_data as $pay_data) {
                    $transaction_id = $pay_data->transaction_id;                    
                    $received_amount=0;$pending_amt=$pay_data->amount;$received_entry_time=NULL;$payment_receive=0;
                    if ($pay_data->idpayment_head == 1) {
                        $transaction_id = "";
                        $received_amount = $pay_data->amount;
                        $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                        $srpayment = array(
                            'date' => $date,
                            'inv_no' => $inv_no,
                            'entry_type' => 1,
                            'idbranch' => $idbranch,
                            'idtable' => $idsale,
                            'table_name' => 'sale',
                            'amount' => $received_amount,
                        );
                        $this->Sale_model->save_daybook_cash_payment($srpayment);
                    }
                    $payment = array(
                        'date' => $date,
                        'idcustomer' => $idcustomer,
                        'idsale' => $idsale,
                        'idbranch' => $idbranch,
                        'idcustomer' => $idcustomer,
                        'idbranch' => $idbranch,
                        'entry_time' => $datetime,
                        'transaction_id' =>$transaction_id,
                        'inv_no' => $inv_no,
                        'idpayment_head' => $pay_data->idpayment_head,
                        'idpayment_mode' => $pay_data->idpayment_mode,
                        'amount' => $pay_data->amount,
                        'created_by' => $iduser,
                        'received_amount' => $received_amount,
                        'received_entry_time'=>$received_entry_time,
                        'payment_receive' => $payment_receive,                        
                    );
                    $other_attr=array();
                    foreach ($pay_data as $key => $value) {
                        if ($key == 'attribute_data') {
                            if ($value != null) {
                                $data = json_decode($value);
                                foreach ($data[0] as $keyy => $valuee) {
                                    $other_attr[$keyy] = $valuee;
                                }
                            }
                        }
                    }
                    if(isset($other_attr)>0){
                        $payment = array_merge($payment, $other_attr); 
                    }
                    $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
                    
                    if($pay_data->credit_type == 0){
                        $npayment = array(
                            'idsale_payment' => $id_sale_payment,
                            'inv_no' => $inv_no,
                            'idsale' => $idsale,
                            'date' => $date,
                            'idcustomer' => $idcustomer,
                            'idbranch' => $idbranch,
                            'amount' => $pay_data->amount,
                            'idpayment_head' => $pay_data->idpayment_head,
                            'idpayment_mode' => $pay_data->idpayment_mode,
                            'transaction_id' => $transaction_id,
                            'created_by' => $iduser,
                            'entry_time' => $datetime,
                            'received_amount' => $received_amount,
                            'pending_amt' => $pending_amt,
                            'received_entry_time'=>$received_entry_time,
                            'payment_receive' => $payment_receive
                        );
                        if(isset($other_attr)>0){
                            $npayment = array_merge($npayment, $other_attr); 
                        }
                        $this->Sale_model->save_payment_reconciliation($npayment);
                    }                    
                }
                 
                //Sale_product             
                $update_stock=array();
                $imei_history=array();
                foreach ($product_data as $product) {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = 0;
                    if ($gst_type == 1) {
                        $igst = $product->igst;
                    } else {
                        $cgst = $product->cgst;
                        $sgst = $product->sgst;
                    }
                    $sale_product = array(
                        'date' => $date,
                        'idsale' => $idsale,
                        'idmodel' => $product->idmodel,
                        'idvariant' => $product->idvariant,
                        'imei_no' => $product->imei_no,
                        'hsn' => $product->hsn,
                        'idskutype' => $product->idsku_type,
                        'idgodown' => $product->idgodown,
                        'idproductcategory' => $product->idproductcategory,
                        'idcategory' => $product->idcategory,
                        'idbrand' => $product->idbrand,
                        'product_name' => $product->full_name,
                        'price' => $product->price,
                        'landing' => $product->landing,
                        'mrp' => $product->mrp,
                        'mop' => $product->mop,
                        'nlc_price' => $product->nlc_price,
                        'ageing' => $product->ageing,
                        'focus' => $product->focus_status,
                        'focus_incentive' => $product->focus_amount,
                        'salesman_price' => $product->salesman_price,
                        'qty' => $product->qty,
                        'inv_no' => $inv_no,
                        'idbranch' => $product->idbranch,
                        'discount_amt' => $product->discount,
                        'is_gst' => $product->is_gst,
                        'is_mop' => $product->is_mop,
                        'basic' => $product->basic,
                        'idvendor' => $product->idvendor,
                        'cgst_per' => $product->cgst,
                        'sgst_per' => $product->sgst,
                        'igst_per' => $product->igst,
                        'total_amount' => $product->sold_amount,
                        'entry_time' => $datetime,       
                        'insurance_imei_no' => $product->proplan_imei,
                        'activation_code' => $product->proplan_activationcode,
                    );
                    $idsaleproduct = $this->Sale_model->save_sale_product($sale_product);
                    
                    if($product->idsku_type == 4){ //qunatity
//                        $this->Sale_model->minus_stock_byidstock($product->id_stock, $product->qty);
                        $update_stock[]="UPDATE stock SET qty = qty - ".$product->qty." WHERE id_stock = ".$product->id_stock.";";
                
                    }else{
                        $this->Api_Model->delete_stock_byidstock($product->id_stock);
                        // IMEI History
                        $imei_history[]=array(
                            'imei_no' => $product->imei_no,
                            'entry_type' => 'Sale',
                            'entry_time' => $datetime,
                            'date' => $date,
                            'idbranch' => $idbranch,
                            'idgodown' => $product->idgodown,
                            'idvariant' => $product->idvariant,
                            'idimei_details_link' => 4, 
                            'idlink' => $idsale,
                            'iduser' => $iduser,
                        );
                    }
                }
               
                if(count($imei_history) > 0){
                    $this->General_model->save_batch_imei_history($imei_history);
                }
                $invoice_data = array( 'invoice_no' => $invid );
                $this->General_model->edit_db_branch($idbranch, $invoice_data);
                if(count($update_stock) > 0){
                    foreach ($update_stock as $data){
                        $this->Api_Model->minus_stock_by_idstock($data);    
                    }                
                }                 
                
                 if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = 204;
                    $result['message'] = 'Fail to generate invoice';
                } else {
                    $this->db->trans_commit();
                    $result['status'] = 200;
                    $result['message'] = 'Invoice generated successfully';
                    $result['data'] = array("invoice_no" => $inv_no, "idsale" => $idsale, "pdf" => 1);
                }
                
            }
            echo json_encode($result);
            }
        }
//                $this->pdf_print($idsale);
               
        //        die('<pre>'.print_r($_POST,1).'</pre>');
//                if($dcprint[0] == 0){
//                    $this->session->set_userdata('idsale_url','invoice_print_14april/'.$idsale);
//                    return redirect('Sale/invoice_print/'.$idsale);
//                }else{
//                    $this->session->set_userdata('idsale_url','dc_print_14april/'.$idsale);
//                    return redirect('Sale/dc_print/'.$idsale);
//                }

    }
    
    public function get_promoter_target(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();
                        $iduser = $this->input->post('iduser');
                        $idbranch = $this->input->post('idbranch');
                        $date=date('Y-m-d');
                        $q['target'] = $this->Api_Model->get_current_promoter_target($iduser,$idbranch,$date);            
                        if (count($q['target'])) {
                            $result['status']=200;
                            $result['message']='Target data found';
                            $result['data']=$q;
                            echo json_encode($result);            
                        } else {
                            $result['status']=204;
                            $result['message']='Target data not available';
                            $result['data']='';
                            echo json_encode($result);
                        }
                    }
            }
    }
    public function get_godown_category_brand(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();
                        $q['godown']=array();
                        $q['product_category']=array();
                        $q['brand']=array();
                        $q['godown'] = $this->General_model->get_active_godown();
                        $q['product_category'] = $this->General_model->get_product_category_data();
                        $q['brand'] = $this->General_model->get_active_brand_data();  
                        $result['status']=200;
                        $result['message']='Godown/Category/Brands Data';
                        $result['data']=$q;
                        echo json_encode($result,JSON_UNESCAPED_SLASHES);   
                    }
            }
    }
	
	public function get_target_product_category(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();                        
                        $q['product_category']=array();
                        $q['product_category'] = $this->Target_model->get_product_category_data();                        
                        $result['status']=200;
                        $result['message']='Godown/Category/Brands Data';
                        $result['data']=$q;
                        echo json_encode($result,JSON_UNESCAPED_SLASHES);   
                    }
            }
    }
	
    public function get_qty_stock(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();
                        $q['qty_stock']=array();
                        $idbranchs=array();
                        $q['qty_stock'] = $this->Stock_model->get_quantity_stock_by_GPBB($this->input->post('idgodown'), $this->input->post('idbrand'), $this->input->post('idproductcategory'), $this->input->post('idbranch'),0,$idbranchs);
                        if (count($q['qty_stock'])>0) {
                            $result['status']=200;
                            $result['message']='Stock available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES); 
                        }else{
                            $result['status']=204;
                            $result['message']='Stock not available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);   
                        }
                    }
            }
    }
    public function get_promoter_sale_data(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();
                        $q['sale_report']=array();                        
                        $q['sale_report'] = $this->Api_Model->ajax_get_promoter_sale_data_byfilter($this->input->post('date_from'),$this->input->post('date_to'),$this->input->post('idbranch'), $this->input->post('idproductcategory'),$this->input->post('idbrand'),$this->input->post('iduser'));
                        if (count($q['sale_report'])>0) {
                            $result['status']=200;
                            $result['message']='Sale report available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES); 
                        }else{
                            $result['status']=204;
                            $result['message']='Sale data not available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);   
                        }
                    }
            }
    }
    public function get_sale_details_bydsale(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $idsale=$this->input->post('idsale');
                        $result=array();
                        $d=array();
                        $q=array();   
                        $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);
                        $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
                        $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
                        $q['sale_reconciliation'] = $this->Sale_model->get_sale_reconciliation_byid($idsale);
                        $q['payment_head_has_attributes'] = $this->General_model->get_payment_head_has_attributes();
                        $d['msale']=$q;
                        if (count($q['sale_data'])>0) {
                            $result['status']=200;
                            $result['message']='Sale report available';
                            $result['data']=$d;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES); 
                        }else{
                            $result['status']=204;
                            $result['message']='Sale data not available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);   
                        }
                    }
            }
    }
     public function get_promoter_payment_summary() {
        $check_auth_client = $this->Api_Model->check_auth_client();
        if ($check_auth_client == true) {
            $auth = $this->Api_Model->auth();
            if ($auth['status'] == 200) {
                $result = array();
                $iduser = $this->input->post('iduser');
                $idbranch = $this->input->post('idbranch');
                $date = $this->input->post('date');
                $q['drr'] = array();
                $drr['columns'] = array();
                $drr['values'] = array();
                $d = $this->Api_Model->get_payment_summary_byidpromoter_date($iduser, $idbranch, $date);
                if (count($d) > 0) {
                    $cols = array();
                    $dr = array();
                    foreach ($d as $dd) {
                        $cols[] = $dd->payment_head;
                        $dr[] = $dd->amt;
                    }
                    array_push($drr['columns'], $cols);
                    array_push($drr['values'], $dr);
                     $q['drr'] = $drr;
                      $result['status'] = 200;
                    $result['message'] = 'Payment Summary';
                    $result['data'] = $q;
                    echo json_encode($result);
                }else {
                    $result['status'] = 204;
                    $result['message'] = 'Payment Summary';
                    $result['data'] = '';
                    echo json_encode($result);
                }
            }
        }
    }

    public function search_invoice_by_filter(){
        $check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){
                        $result=array();
                        $d=array();
                        $q=array();   
                        $q['sale_data']=array();
                        $imei = $this->input->post('imei');
                        $contact_no = $this->input->post('contact_no');
                        $invoice_no = $this->input->post('invoice_no');
                        if($imei !=""){
                            $q['sale_data'] = $this->Sale_model->ajax_get_sales_data_byimei($imei);                        
                        }else if($contact_no != ""){
                            $q['sale_data'] = $this->Sale_model->ajax_get_sales_data_bycontact($contact_no);                            
                        }else if($invoice_no != ""){
                            $q['sale_data'] = $this->Sale_model->ajax_get_sales_data_byinvoice($invoice_no);                            
                        }else{
                            
                        }      
                         $d['msale']=$q;
                        if (count($q['sale_data'])>0) {
                            $result['status']=200;
                            $result['message']='Sale report available';
                            $result['data']=$d;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES); 
                        }else{
                            $result['status']=204;
                            $result['message']='Sale data not available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);   
                        }
                    }
            }
    }
    
    public function ajax_get_drr_promotor_sale_report_slab(){
        
        $idpromoter = $this->input->post('iduser');
        $from = $this->input->post('from');        
        $idproductcategory = $this->input->post('idproductcategory');        
        $idbranch = $this->input->post('idbranch');        
        $allpcats=array();
        $promotor_target_slab_data = $this->Api_Model->get_current_promoter_target($idpromoter,$idbranch,$from);  
        $q['drr']=array();
        $drr['values']=array();
        $drr['columns']=array();
        if(count($promotor_target_slab_data)>0){
        
        $slabmonth = date("Y-m",strtotime($from));     
        $idslab = $promotor_target_slab_data[0]->id_targetslab;                
        $target_slab_data = $this->Target_model->get_slab_by_month($slabmonth);        
        $slabcnt = count($target_slab_data);                      
            if($idslab == '0' ||  $idslab == 0){ 
                $from_slab = $slabmonth.'-01';
                $to_slab = date('Y-m-t', strtotime($from));    
            }else{
                $slab_data = $this->Target_model->get_target_slab_data_byid($idslab);
                $from_slab = $promotor_target_slab_data[0]->from_slab;
                $to_slab = $promotor_target_slab_data[0]->to_slab;
            }

            $first_date = $from;
            $last_date = date('d', strtotime($to_slab));
            
            //selected Date
//            $end_date = date('d', strtotime($from));
            $end_date = date('d', strtotime('-1 day', strtotime($from)));
            if(date('d', strtotime($from)) == 01){
                $remaining_days = date('d', strtotime($to_slab));
            }else{
                $remaining_days =  $last_date - $end_date;
            }
            
            $sale_data = $this->Api_Model->get_drr_promotor_sale_report_slab_byidpromoter($from,$from_slab,$idslab, $idproductcategory, $allpcats, $idbranch,$idpromoter,1);
//            die('<pre>'.print_r($sale_data,1).'</pre>');
           
            if($sale_data){ 
                
               
                $cols=array();
               /* $cols[]='ZONE';
                $cols[]='BRANCH';
                $cols[]='PARTNER TYPE';
                $cols[]='BRANCH CATEGORY'; */
                $cols[]='PROMOTER NAME';
	        $cols[]='PROMOTER BRAND';                
                $cols[]='VOLUME TARGET';
                $cols[]='VOLUME ACH';
                $cols[]='ACH(%)';
                $cols[]='VALUE TARGET';
                $cols[]='VALUE ACH';
                $cols[]='ACH(%)';
                $cols[]='ASP TARGET';
                $cols[]='ASP ACH';                
                $cols[]='ACH(%)';
                $cols[]='REVENUE TARGET';
                $cols[]='REVENUE ACH';

                array_push($drr['columns'], $cols);
                        
                        $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                        $tvol=0;$tsal=0;$tval=0;$tsa_total=0;
                        $volume_target = 0;$value_target = 0;
                        $c_saleqty=0; $c_saletotal=0; $c_landing=0;
                        $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                        $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                        $num_cnt=0;                        
                        $last_tar_per=0; $last_tar_vol = 0;$last_tar_val=0;  $last_sale_qty=0; $last_sale_total=0;
                        $last_tar_vol_gap=0;$tar_vol_gap=0;
                        $last_tar_val_gap=0;$tar_val_gap=0;
                        
                        foreach ($sale_data as $sale){                             
                       //******Last Target Slab Value Start **************                           
                            $last_tar_vol = $sale->last_pvolume;
                            $last_tar_val = $sale->last_pvalue;
                            
                            if( $sale->last_csale_qty > 0){ $last_sale_qty = $sale->last_csale_qty;  } else{ $last_sale_qty = 0;};
                            if( $sale->last_ctotal > 0){ $last_sale_total = round($sale->last_ctotal); } else{ $last_sale_total = 0;};
                            
                            $tar_vol_gap = $last_tar_vol - $last_sale_qty;
                            $tar_val_gap = $last_tar_val - $last_sale_total;
                            
                            if($tar_vol_gap > 0){
                                $last_tar_vol_gap = $tar_vol_gap;
                                $last_tar_val_gap = $tar_val_gap;
                            }else{
                                $last_tar_vol_gap = 0;
                                $last_tar_val_gap = 0;
                            }
                            
                            if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                            if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                            if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                            if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                            if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                            if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                            if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                            if($sale->csale_qty){ $c_saleqty = $sale->csale_qty;}else{ $c_saleqty = 0; }
                            if($sale->ctotal){ $c_saletotal = $sale->ctotal;}else{ $c_saletotal = 0; }
                            if($sale->clanding){ $c_landing = $sale->clanding;}else{ $c_landing = 0; }

                            if($remaining_days != 0){
                                $volume_target = round((($vol + $last_tar_vol_gap) - $c_saleqty)/$remaining_days,0);
                                $value_target = round((($vall + $last_tar_val_gap) - $c_saletotal)/$remaining_days,0);
                            }else{ 
                                $volume_target = 0;
                                $value_target = 0;
                            }
                            if($volume_target != 0){
                                $vol_ach = ($salqt / $volume_target)*100;
                            }else{
                                $vol_ach =0;
                            }
                            if($value_target != 0){
                                $val_ach = ($saletot / $value_target)*100;
                            }else{
                                $val_ach =0;
                            }
                            if($salqt > 0){
                                $asp_ach = ($saletot/ $salqt);
                            }else{
                                $asp_ach =0;
                            }
                            //Target Achivement Per
                            if($asp > 0){
                                $asp_ach_per = ($asp_ach/$asp)*100;
                            }else{
                                $asp_ach_per = 0;
                            }
                          //Revenue Percentage  
                            if($slanding > 0){
                                $rev_per = (($saletot - $slanding)*100)/$slanding;
                            }else{
                                $rev_per = 0;
                            }
                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);                            
                            $dr=array();
                           /* $dr[]=$sale->zone_name;
                            $dr[]=$sale->branch_name;
                            $dr[]=$sale->partner_type;
                            $dr[]=$sale->branch_category_name;*/
                            $brand_name="";
                            if($brand_data){ $brand_name = $brand_data->brand_name;}
                            $dr[]=$sale->user_name;                           
                            $dr[]=$brand_name;                            
                            $dr[]=round($volume_target,2);
                            $dr[]=$salqt;
                            $dr[]=round($vol_ach,1).'%';
                            $dr[]=round($value_target,1);
                            $dr[]=$saletot;
                            $dr[]=round($val_ach,1).'%';
                            $dr[]=round($asp,0);
                            $dr[]=round($asp_ach,1);
                            $dr[]=round($asp_ach_per,1).'%';
                            $dr[]=round($recvenue,2).'%';
                            $dr[]=round($rev_per,2).'%';
                            array_push($drr['values'], $dr);
                            } 
                            $q['drr']=$drr;							
                            $result['status']=200;
                            $result['message']='DRR Available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES); 
                    }else{
                            $q['drr']=$drr;	
                            $result['status']=204;
                            $result['message']='DRR Not Available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);
                    }  
                     }else{
                            $q['drr']=$drr;	
                            $result['status']=204;
                            $result['message']='DRR Not Available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);
                    } 
    }
    
     public function ajax_get_mtd_promotor_sale_report_slab() {

        $idpromoter = $this->input->post('iduser');
        $to = $this->input->post('from');        
        $idproductcategory = $this->input->post('idproductcategory');        
        $idbranch = $this->input->post('idbranch');        
        $allpcats=array();
        
        
        $q['drr']=array();
        $drr['values']=array();
        $drr['columns']=array();                
        $slabmonth = date("Y-m",strtotime($to));                                
        $from_slab = $slabmonth.'-01';
            $sale_data = $this->Api_Model->get_promotor_sale_report_byidbranch_idpromoter($from_slab, $to,$idproductcategory,$idbranch,$idpromoter);
    //        die('<pre>'.print_r($sale_data,1).'</pre>');
            if($sale_data){ 
                
                $cols=array();
               /* $cols[]='ZONE';
                $cols[]='BRANCH';
                $cols[]='PARTNER TYPE';
                $cols[]='BRANCH CATEGORY'; */
                $cols[]='PROMOTER NAME';
	        $cols[]='PROMOTER BRAND';                
                $cols[]='VOLUME TARGET';
                $cols[]='VOLUME ACH';
                $cols[]='ACH(%)';
                $cols[]='VALUE TARGET';
                $cols[]='VALUE ACH';
                $cols[]='ACH(%)';
                $cols[]='ASP TARGET';
                $cols[]='ASP ACH';                
                $cols[]='ACH(%)';
                $cols[]='REVENUE TARGET';
                $cols[]='REVENUE ACH';

                    array_push($drr['columns'], $cols);
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;
                    foreach ($sale_data as $sale){                             
                            if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                            if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                            if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                            if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                            if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                            if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                            if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                            if($vol > 0){
                                $vol_ach = ($salqt / $vol)*100;
                            }else{
                                $vol_ach =0;
                            }
                            if($vall > 0){
                                $val_ach = ($saletot / $vall)*100;
                            }else{
                                $val_ach =0;
                            }
                            if($salqt > 0){
                                $asp_ach = ($saletot/ $salqt);
                            }else{
                                $asp_ach =0;
                            }
                            //Target Achivement Per
                            if($asp > 0){
                                $asp_ach_per = ($asp_ach/$asp)*100;
                            }else{
                                $asp_ach_per = 0;
                            }
                          //Revenue Percentage  
                            if($slanding > 0){
                                $rev_per = (($saletot - $slanding)*100)/$slanding;
                            }else{
                                $rev_per = 0;
                            }
                            $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);                            
                            $dr=array();
                           /* $dr[]=$sale->zone_name;
                            $dr[]=$sale->branch_name;
                            $dr[]=$sale->partner_type;
                            $dr[]=$sale->branch_category_name;*/
                            $brand_name="";
                            if($brand_data){ $brand_name = $brand_data->brand_name;}
                            $dr[]=$sale->user_name;                           
                            $dr[]=$brand_name;                            
                            $dr[]=$vol;
                            $dr[]=$salqt;
                            $dr[]=round($vol_ach,1).'%';
                            $dr[]=$vall;
                            $dr[]=$saletot;
                            $dr[]=round($val_ach,1).'%';
                            $dr[]=round($asp,1);
                            $dr[]=round($asp_ach,1);
                            $dr[]=round($asp_ach_per,1).'%';
                            $dr[]=round($recvenue,2).'%';
                            $dr[]=round($rev_per,2).'%';
                            array_push($drr['values'], $dr);
                            } 
                            $q['drr']=$drr;							
                            $result['status']=200;
                            $result['message']='MTD Available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES); 
                    }else{
                            $q['drr']=$drr;	
                            $result['status']=204;
                            $result['message']='MTD Not Available';
                            $result['data']=$q;
                            echo json_encode($result,JSON_UNESCAPED_SLASHES);
                    }  
                    
    }
    
    public function ajax_get_lmtd_promotor_sale_report_byidbranch_idpromoter() {
           $month = $this->input->post('current_month');
        $lastmonth = $this->input->post('last_month');
        $idpromoter = $this->input->post('iduser');             
        $idpcat = $this->input->post('idproductcategory');          
        $idbranch = $this->input->post('idbranch'); 
        $q['drr']=array();
        $drr['values']=array();
        $drr['columns']=array();
        $sale_data = $this->Api_Model->get_lmtd_promotor_sale_report_byidbranch_idpromoter($month,$lastmonth, $idpcat,$idbranch,$idpromoter);
        
        if($sale_data){
             $cols=array();
               /* $cols[]='ZONE';
                $cols[]='BRANCH';
                $cols[]='PARTNER TYPE';
                $cols[]='BRANCH CATEGORY'; */
                $cols[]='PROMOTER NAME';
	        $cols[]='PROMOTER BRAND';                
                $cols[]='LMTD VOLUME';
                $cols[]='MTD VOLUME';
                $cols[]='GAP(%)';
                $cols[]='LMTD VALUE';
                $cols[]='MTD VALUE';
                $cols[]='GAP(%)';
                $cols[]='LMTD ASP';
                $cols[]='MTD ASP';                
                $cols[]='GAP(%)';
                $cols[]='LMTD REVENUE';
                $cols[]='MTD REVENUE';
                $cols[]='GAP(%)';
                array_push($drr['columns'], $cols);
            ?>
            
                    <?php $sr=1; 
                    $lmtd_volume =0; $lmtd_value =0;$smart_volume=0;$smart_value=0;
                    $mtd_volume=0;$mtd_value =0;$mtd_smart_volume=0;$mtd_smart_value=0;
                    $volume_gap =0;$value_gap=0;$asp_gap=0;
                    $lmtd_asp = 0;$mtd_asp=0;
                    $t_lmtd_volume=0;$t_mtd_volume=0;$t_gap_volume=0; $t_lmtd_asp=0;
                    $t_lmtd_value=0;$t_mtd_value=0;$t_gap_value=0;$t_mtd_asp = 0; $t_asp_gap=0;
                    
                    $lmtd_landing = 0;$mtd_landing=0;$smart_landing=0;
                    $lmtd_rev_per = 0;$mtd_rev_per=0;$rev_gap=0;$tlmtd_rev=0;$tmtd_rev=0;$t_rev_gap=0;
                    
                    $t_lmtd_landing =0; $t_mtd_landing=0;
                    foreach ($sale_data as $sale){
                         $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->sale_qty){ $mtd_volume = $sale->sale_qty;}else{$mtd_volume=0;}
                        if($sale->sale_total){$mtd_value = $sale->sale_total;}else{ $mtd_value =0; }
                        
                        if($sale->lsale_qty){ $lmtd_volume = $sale->lsale_qty;}else{ $lmtd_volume=0; }
                        if($sale->last_sale_total){  $lmtd_value = $sale->last_sale_total; } else{ $lmtd_value = 0;} 
                        
                        if($sale->smart_sale_qty){$mtd_smart_volume  = $sale->smart_sale_qty;}else{ $mtd_smart_volume =0; }
                        if($sale->smart_total){$mtd_smart_value = $sale->smart_total;}else{ $mtd_smart_value= 0;}
                        
                        if($sale->lsmart_sale_qty){  $smart_volume = $sale->lsmart_sale_qty;}else{$smart_volume =0;}
                        if($sale->lsmart_total){ $smart_value = $sale->lsmart_total;}else{ $smart_value =0 ;}
                        
                        if($sale->sale_landing){$mtd_landing  = $sale->sale_landing;}else{ $mtd_landing =0; }
                        if($sale->last_sale_landing){ $lmtd_landing  = $sale->last_sale_landing; } else{ $lmtd_landing = 0;} 
                        if($sale->lsmart_landing){$smart_landing = $sale->lsmart_landing;}else{$smart_landing = 0;}
                        
                        if($idpcat == 1){
                            if($smart_volume != 0 || $smart_volume!= ''){ $lmtd_asp = ($smart_value/$smart_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_smart_volume != 0 || $mtd_smart_volume!= ''){ $mtd_asp = ($mtd_smart_value/$mtd_smart_volume);}else{ $mtd_asp = 0;}
                        }else{
                            if($lmtd_volume != 0 || $lmtd_volume!= ''){ $lmtd_asp = ($lmtd_value/$lmtd_volume);}else{ $lmtd_asp = 0;}
                            if($mtd_volume != 0 || $mtd_volume!= ''){ $mtd_asp = ($mtd_value/$mtd_volume);}else{ $mtd_asp = 0;}
                        }                       
                        
                        if($lmtd_volume != 0){ $volume_gap = ((($mtd_volume - $lmtd_volume)/$lmtd_volume)*100); }else{ $volume_gap = 0; }
                        if($lmtd_value != 0){ $value_gap = ((($mtd_value - $lmtd_value)/$lmtd_value)*100); }else{ $value_gap = 0; }
                        
                        if($lmtd_asp != 0){ $asp_gap = ((($mtd_asp - $lmtd_asp)/$lmtd_asp)*100); }else{ $asp_gap = 0; }
                        
                        if($lmtd_landing > 0){
                            $lmtd_rev_per = (($lmtd_value - $lmtd_landing)*100)/$lmtd_landing;
                        }else{
                            $lmtd_rev_per = 0;
                        }
                         if($mtd_landing > 0){
                            $mtd_rev_per = (($mtd_value - $mtd_landing)*100)/$mtd_landing;
                        }else{
                            $mtd_rev_per = 0;
                        }
                        
                        if($lmtd_rev_per != 0){ $rev_gap = (($mtd_rev_per - $lmtd_rev_per)/$lmtd_rev_per)*100;}else{ $rev_gap = 0; }
                        
                         $dr=array();
                           /* $dr[]=$sale->zone_name;
                            $dr[]=$sale->branch_name;
                            $dr[]=$sale->partner_type;
                            $dr[]=$sale->branch_category_name;*/
                            $brand_name="";
                            if($brand_data){ $brand_name = $brand_data->brand_name;}
                            $dr[]=$sale->user_name;                           
                            $dr[]=$brand_name;                            
                            $dr[]=round($lmtd_volume,0);
                            $dr[]=round($mtd_volume,0);
                            $dr[]=round($volume_gap,1).'%';
                            $dr[]=round($lmtd_value,0);
                            $dr[]=round($mtd_value,0);
                            $dr[]=round($value_gap,1).'%';
                            $dr[]=round($lmtd_asp,0);
                            $dr[]=round($mtd_asp,0);
                            $dr[]=round($asp_gap,1).'%';
                            $dr[]=round($lmtd_rev_per,2).'%';
                            $dr[]=round($mtd_rev_per,2).'%';
                            $dr[]=round($rev_gap,2).'%';
                            array_push($drr['values'], $dr);
                     } 
                     
                    $q['drr']=$drr;							
                    $result['status']=200;
                    $result['message']='LMTD vs MTD Available';
                    $result['data']=$q;
                    echo json_encode($result,JSON_UNESCAPED_SLASHES);
                }else{
                    $q['drr']=$drr;	
                    $result['status']=204;
                    $result['message']='LMTD vs MTD Not Available';
                    $result['data']=$q;
                    echo json_encode($result,JSON_UNESCAPED_SLASHES);
                }     
    }
    
    public function get_promotor_target_vs_ach_byidbranch_idpromoter() {
        $monthyear = $this->input->post('monthyear');
        $idpromoter = $this->input->post('iduser');             
        $idpcat = $this->input->post('idproductcategory');          
        $idbranch = $this->input->post('idbranch'); 
        $q['drr']=array();
        $drr['values']=array();
        $drr['columns']=array();
        $sale_data = $this->Api_Model->get_promotor_target_ach_byidbranch_idpromoter($monthyear, $idpcat, $idbranch, $idpromoter);
//        die('<pre>'.print_r($sale_data,1).'</pre>');
        if($sale_data){ 
                $cols=array(); 
               /* $cols[]='ZONE'; 
                $cols[]='BRANCH';
                $cols[]='PARTNER TYPE';
                $cols[]='BRANCH CATEGORY'; */
                $cols[]='PROMOTER NAME';
	        $cols[]='PROMOTER BRAND';    
                $cols[]='';
                $cols[]='VOLUME';
                $cols[]='TARGET';
                $cols[]='ACH';
                $cols[]='ACH(%)';
                $cols[]='';
                $cols[]='VALUE';
                $cols[]='TARGET';
                $cols[]='ACH';
                $cols[]='ACH(%)';
                $cols[]='';
                $cols[]='ASP';
                $cols[]='TARGET';
                $cols[]='ACH';
                $cols[]='ACH(%)';
                $cols[]='';
                $cols[]='REVENUE';
                $cols[]='TARGET';
                $cols[]='ACH';
                $cols[]='ACH(%)';
                $cols[]='';
                $cols[]='FINANCE';
                $cols[]='SMART PHONE';
                $cols[]='FINANCE ACH';
                $cols[]='CONN(%)';
                $cols[]='';
                $cols[]='RUDRAM';
                $cols[]='SMART PHONE';
                $cols[]='ACH';
                $cols[]='CONN(%)';
                $cols[]='';
                $cols[]='AGING';
                $cols[]='TARGET';
                $cols[]='ACH';
                $cols[]='ACH(%)';
                array_push($drr['columns'], $cols);
            
                
                    $vol =0;$vall=0; $vol_ach=0;$val_ach=0;$salqt=0;$saletot=0;
                    $tvol=0;$tsal=0;$tval=0;$tsa_total=0; $t_smart_phone=0;$t_finance=0;$t_rudram=0;
                    $asp = 0;$recvenue=0;$slanding=0;$asp_ach=0;$asp_ach_per=0;$rev_per=0;$rev_ach_per=0;
                    $t_asp=0;$t_asp_ach=0;$t_rev = 0;$t_sale_landing=0;
                    
                    $smart_phone=0;$finance_qty=0;$rud_qty;$fin_conn=0;$rud_conn=0; $num_cnt=0;
                    foreach ($sale_data as $sale){ 
                        $brand_data = $this->Target_model->get_brand_data_byidpromotor($sale->id_users);
                        
                        if($sale->pvolume){ $vol = $sale->pvolume;}else{ $vol = 0;}
                        if($sale->pvalue){$vall = $sale->pvalue; } else{ $vall = 0;}
                        if($sale->sale_qty){ $salqt = $sale->sale_qty;}else{ $salqt = 0; }
                        if($sale->total){ $saletot = $sale->total;}else{ $saletot = 0; }
                        if($sale->landing){ $slanding = $sale->landing;}else{ $slanding = 0; }
                        if($sale->pasp){$asp = $sale->pasp; } else{ $asp = 0;}
                        if($sale->prevenue){$recvenue = $sale->prevenue; } else{ $recvenue = 0;}
                        
                        if($sale->smart_qty){$smart_phone = $sale->smart_qty; } else{ $smart_phone = 0;}
                        if($sale->finance_qty){$finance_qty = $sale->finance_qty; } else{ $finance_qty = 0;}
                        if($sale->rudram_qty){$rud_qty = $sale->rudram_qty; } else{ $rud_qty = 0;}
                        
                        if($vol > 0){
                            $vol_ach = ($salqt / $vol)*100;
                        }else{
                            $vol_ach =0;
                        }
                        
                        if($vall > 0){
                            $val_ach = ($saletot / $vall)*100;
                        }else{
                            $val_ach =0;
                        }
                        if($salqt > 0){
                            $asp_ach = ($saletot/ $salqt);
                        }else{
                            $asp_ach =0;
                        }
                        
                        //Target Achivement Per
                        if($asp > 0){
                            $asp_ach_per = ($asp_ach/$asp)*100;
                        }else{
                            $asp_ach_per = 0;
                        }
                        
                      //Revenue Percentage  
                        if($slanding > 0){
                            $rev_per = (($saletot - $slanding)*100)/$slanding;
                        }else{
                            $rev_per = 0;
                        }
                        //revenue ach per 
                        if($recvenue > 0){
                            $rev_ach_per = ($rev_per/$recvenue)*100;
                        }else{
                            $rev_ach_per = 0;
                        }
                        //finance conn
                        if($smart_phone > 0){
                            $fin_conn = ($finance_qty/$smart_phone)*100;
                            $rud_conn = ($rud_qty/$smart_phone)*100;
                        }else{
                            $fin_conn = 0;
                            $rud_conn =0;
                        }
                        
                         $dr=array();
                           /* $dr[]=$sale->zone_name;
                            $dr[]=$sale->branch_name;
                            $dr[]=$sale->partner_type;
                            $dr[]=$sale->branch_category_name;*/
                            $brand_name="";
                            if($brand_data){ $brand_name = $brand_data->brand_name;}
                            $dr[]=$sale->user_name;                           
                            $dr[]=$brand_name;                            
                            $dr[]='';
                            $dr[]='';
                            $dr[]=$vol;
                            $dr[]=$salqt;
                            $dr[]=round($vol_ach,1).'%';
                            $dr[]='';
                            $dr[]='';
                            $dr[]=$vall;
                            $dr[]=$saletot;
                            $dr[]=round($val_ach,1).'%';
                            $dr[]='';
                            $dr[]='';
                            $dr[]=round($asp,0);
                            $dr[]=round($asp_ach,0);
                            $dr[]=round($asp_ach_per,0).'%';
                            $dr[]='';
                            $dr[]='';
                            $dr[]=round($recvenue,2);
                            $dr[]=round($rev_per,2).'%';
                            $dr[]=round($rev_ach_per,2).'%';
                            $dr[]='';
                            $dr[]='';
                            $dr[]=$smart_phone;
                            $dr[]=$finance_qty;
                            $dr[]=round($fin_conn,0).'%';
                            $dr[]='';
                            $dr[]='';
                            $dr[]=$smart_phone;
                            $dr[]=$rud_qty;
                            $dr[]=round($rud_conn,0).'%';
                            $dr[]='';
                            $dr[]='';
                            $dr[]='0';
                            $dr[]='0';
                            $dr[]='0%';                            
                            array_push($drr['values'], $dr);
                        } 
                        $q['drr']=$drr;							
                        $result['status']=200;
                        $result['message']='Target vs Ach Available';
                        $result['data']=$q;
                        echo json_encode($result,JSON_UNESCAPED_SLASHES);
                        
              }else{
                   $q['drr']=$drr;	
                    $result['status']=204;
                    $result['message']='Target vs Ach Not Available';
                    $result['data']=$q;
                    echo json_encode($result,JSON_UNESCAPED_SLASHES);
        }
        
    }
    
     public function get_promotor_target_setup_report_byidpromoter(){
        $idpromoter =$this->input->post('iduser');        
        $monthyear = $this->input->post('monthyear');
        $idpcat = $this->input->post('idproductcategory');
        $idbranch = $this->input->post('idbranch');
        $q['drr']=array();
        $drr['values']=array();
        $drr['columns']=array();
        $cols = array();
        $cols[] = 'PROMOTER NAME';
        $cols[] = 'PROMOTER BRAND';
        $target_slab_data = $this->Target_model->get_slab_by_month($monthyear);
        $slab_cnt= count($target_slab_data);
        foreach ($target_slab_data as $t_data) {
            $cols[] = '';
            $cols[] = $t_data->slab_name;            
            if ($idpcat != 6) {
                $cols[] = 'VOLUME';
                $cols[] = 'VALUE';
                $cols[] = 'ASP';
                $cols[] = 'REVENUE';
            } else {
                $cols[] = 'VALUE';
                $cols[] = 'CONNECT';
            }
        }
            $cols[] = '';
            $cols[] = 'Month';            
            if ($idpcat != 6) {
                $cols[] = 'VOLUME';
                $cols[] = 'VALUE';
                $cols[] = 'ASP';
                $cols[] = 'REVENUE';
            } else {
                $cols[] = 'VALUE';
                $cols[] = 'CONNECT';
            }
        
        array_push($drr['columns'], $cols);        
        $promotor_data = $this->Api_Model->get_promotor_target_setup_data($monthyear,$idpcat,$idpromoter,$idbranch);  
        
        if($promotor_data){ 
            
           ?>
              
                        <?php $sr=1;$tvol=0;$tval=0;$tasp=0;$tre=0;$tcon=0;
                        
                         $dr=array();
                         $dr[]=$promotor_data[0]->user_name;
                         $dr[]=$promotor_data[0]->brand_name;
                        foreach($promotor_data as $pdata){ 
                            $dr[]='';
                            $dr[]='';
                            if($idpcat != 6){
                               $dr[]=$pdata->volume; $tvol = $tvol + $pdata->volume;
                               $dr[]=$pdata->value; $tval = $tval + $pdata->value ;
                               $dr[]=$pdata->asp; 
                               $dr[]=$pdata->revenue; $tre = $tre + $pdata->revenue;
                            }else{
                               $dr[]=$pdata->volume; $tvol = $tvol + $pdata->volume;
                               $dr[]=$pdata->connect; $tcon = $tcon + $pdata->connect;
                            }
                            
                          } 
                                $dr[]='';
                                $dr[]='';
                             if($idpcat != 6){
                                $dr[]=$tvol."";
                                $dr[]=$tval."";
                                $dr[]= round(($tval/$tvol),0)."";
                                $dr[]=round(($tre/$slab_cnt),0)."";
                             }else{
                                $dr[]=$tvol."";
                                $dr[]=round(($tcon/$slab_cnt),0)."";
                             }
                             array_push($drr['values'], $dr);
                        
                        $q['drr']=$drr;							
                        $result['status']=200;
                        $result['message']='Target Data Available';
                        $result['data']=$q;
                        echo json_encode($result,JSON_UNESCAPED_SLASHES);
                        
            }else{ 
                    $q['drr']=$drr;	
                    $result['status']=204;
                    $result['message']='Target Data Not Available';
                    $result['data']=$q;
                    echo json_encode($result,JSON_UNESCAPED_SLASHES);
            }
    }
    
    
    public function pdf_print($idsale) {
            $q['tab_active'] = '';
            $q['sale_data'] = $this->Sale_model->get_sale_byid($idsale);        
            $q['sale_product'] = $this->Sale_model->get_sale_product_byid($idsale);
//            $name="pdf_".$q['sale_data'][0]->idbranch.$idsale;
            $name="inv_".$q['sale_data'][0]->idbranch.'_'.$idsale;
            $inv_date=$q['sale_data'][0]->date;
            $month = date('F', strtotime($inv_date));
            $year = date('Y', strtotime($inv_date));
            if (!file_exists('Invoices/'.$month.$year)) {
                mkdir('Invoices/'.$month.$year, 0777, true);
            }
        $path="Invoices/".$month.$year."/".$name.".pdf";    
         if(file_exists($path)){
              $this->sendsms($q['sale_data'][0]->customer_contact, $path);
         }else{
            if($q['sale_data'][0]->dcprint){
                $this->load->library('pdf');
                $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
                $q['financer_of_idsale'] = $this->Sale_model->get_financer_of_idsale($idsale);                
                $html = $this->load->view('sale/dc_pdf', $q, true);
                $r =  $this->pdf->createPDF($html, $path, false); 
            }else{
                $q['sale_payment'] = $this->Sale_model->get_sale_payment_byid($idsale);
                $q['financer_of_idsale'] = $this->Sale_model->get_financer_of_idsale($idsale);
                $this->load->library('pdf');
                $html = $this->load->view('sale/invoice_pdf', $q, true);
                $r =  $this->pdf->createPDF($html, $path, false); 
                if($r){
                    $this->sendsms($q['sale_data'][0]->customer_contact, $path);
                }
            }
        }
   }
    
    public function bajaj_finance_integration() { 
        /*$check_auth_client = $this->Api_Model->check_auth_client();
            if ($check_auth_client == true) {
                $auth = $this->Api_Model->auth();
                    if($auth['status'] == 200){*/
                        $sfid = $this->input->post('sfid');
                        $bfl_store_id = $this->input->post('bfl_store_id');
                        $bfl_data = $this->Sale_model->bfl_integration($sfid, $bfl_store_id);
                        echo json_encode($bfl_data);
                 /*   }
            }*/
    }
    
     function sendsms($mobileno, $path){        
                $longurl=base_url().$path;                
                $url=$this->Api_Model->short_url($longurl);                
                $message = "Dear Customer,%0aThank you for shopping with us. Download you invoice from below link.%0a".$url['shortLink'].'%0a- SS MOBILE';
                $message = str_replace(' ', '%20', $message); // replace all spaces with %20 from message                
                
                $baseurl_http='http://login.smsozone.com/api/mt/SendSMS?user=sscommunications&password=sscommunications@7654321&senderid=SSMOBS&channel=Trans&DCS=0&flashsms=0&number='.$mobileno.'&text='.$message.'&route=2069';

                $ch=curl_init($baseurl_http);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response=curl_exec($ch);
                  
                curl_close($ch);  
               
    }
    
    function echoResponse($response) {
        echo '{"status":"' . $response['status'] . '", "message":"' . $response['message'] . '", "data": ' . rtrim(json_encode($response['data'])) . '}';
    }
    function json_output($statusHeader,$response)
	{
		$ci =& get_instance();
		$ci->output->set_content_type('application/json');
		$ci->output->set_status_header($statusHeader);
		$ci->output->set_output(json_encode($response));
	}
        
        
        /* Model - General_model
         * get_active_billing_payment_mode_byhead
         * libraries - Pdf
         * 
         *          */
        
        
    
    

}
