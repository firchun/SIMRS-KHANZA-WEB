<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Satusehat\Integration\OAuth2Client;
use Satusehat\Integration\FHIR\Encounter;
use Satusehat\Integration\FHIR\Condition;
use Satusehat\Integration\FHIR\Observation;
use Satusehat\Integration\FHIR\Patient;
use Satusehat\Integration\FHIR\Organization;
use Satusehat\Integration\FHIR\Location;
use Satusehat\Integration\FHIR\Practitioner;
use Satusehat\Integration\FHIR\Bundle;

class SatuSehatController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new OAuth2Client();
    }

    public function dashboard(Request $request)
    {
        $tgl1 = $request->get('tgl1', date('Y-m-d', strtotime('-30 days')));
        $tgl2 = $request->get('tgl2', date('Y-m-d'));

        $counts = [];
        $total = 0;

        $resources = [
            [
                'key' => 'encounter',
                'label' => 'Encounter',
                'count' => DB::table('satu_sehat_encounter')
                    ->join('reg_periksa', 'satu_sehat_encounter.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'condition',
                'label' => 'Condition',
                'count' => DB::table('satu_sehat_condition')
                    ->join('reg_periksa', 'satu_sehat_condition.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'observation',
                'label' => 'Observation',
                'count' => DB::table('satu_sehat_observationttvbb')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvgcs')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvkesadaran')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvlp')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvnadi')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvrespirasi')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvspo2')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvsuhu')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvtb')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observationttvtensi')->whereBetween('tgl_perawatan', [$tgl1, $tgl2])->count()
                    + DB::table('satu_sehat_observation_lab')->count()
                    + DB::table('satu_sehat_observation_lab_mb')->count()
                    + DB::table('satu_sehat_observation_radiologi')->count(),
            ],
            [
                'key' => 'procedure',
                'label' => 'Procedure',
                'count' => DB::table('satu_sehat_procedure')
                    ->join('reg_periksa', 'satu_sehat_procedure.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'medication',
                'label' => 'Medication',
                'count' => DB::table('satu_sehat_medication')->count(),
            ],
            [
                'key' => 'medication-request',
                'label' => 'MedicationRequest',
                'count' => DB::table('satu_sehat_medicationrequest')->count()
                    + DB::table('satu_sehat_medicationrequest_racikan')->count(),
            ],
            [
                'key' => 'medication-dispense',
                'label' => 'MedicationDispense',
                'count' => DB::table('satu_sehat_medicationdispense')
                    ->whereBetween('tgl_perawatan', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'service-request',
                'label' => 'ServiceRequest',
                'count' => DB::table('satu_sehat_servicerequest_lab')->count()
                    + DB::table('satu_sehat_servicerequest_lab_mb')->count()
                    + DB::table('satu_sehat_servicerequest_radiologi')->count(),
            ],
            [
                'key' => 'clinical-impression',
                'label' => 'ClinicalImpression',
                'count' => DB::table('satu_sehat_clinicalimpression')
                    ->join('reg_periksa', 'satu_sehat_clinicalimpression.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'immunization',
                'label' => 'Immunization',
                'count' => DB::table('satu_sehat_immunization')
                    ->whereBetween('tgl_perawatan', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'medication-statement',
                'label' => 'MedicationStatement',
                'count' => DB::table('satu_sehat_medicationstatement')->count()
                    + DB::table('satu_sehat_medicationstatement_racikan')->count(),
            ],
            [
                'key' => 'care-plan',
                'label' => 'CarePlan',
                'count' => DB::table('satu_sehat_careplan')
                    ->join('reg_periksa', 'satu_sehat_careplan.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'specimen',
                'label' => 'Specimen',
                'count' => DB::table('satu_sehat_specimen_lab')->count()
                    + DB::table('satu_sehat_specimen_lab_mb')->count()
                    + DB::table('satu_sehat_specimen_radiologi')->count(),
            ],
            [
                'key' => 'diagnostic-report',
                'label' => 'DiagnosticReport',
                'count' => DB::table('satu_sehat_diagnosticreport_lab')->count()
                    + DB::table('satu_sehat_diagnosticreport_lab_mb')->count()
                    + DB::table('satu_sehat_diagnosticreport_radiologi')->count(),
            ],
            [
                'key' => 'episode-of-care',
                'label' => 'EpisodeOfCare',
                'count' => DB::table('satu_sehat_episodeofcare')
                    ->join('reg_periksa', 'satu_sehat_episodeofcare.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl1, $tgl2])
                    ->count(),
            ],
            [
                'key' => 'diet',
                'label' => 'Diet',
                'count' => DB::table('satu_sehat_diet')
                    ->whereBetween('tanggal', [$tgl1, $tgl2])
                    ->count(),
            ],
        ];

        foreach ($resources as $r) {
            $total += $r['count'];
        }

        return response()->json([
            'data' => $resources,
            'total' => $total,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ]);
    }

    public function getById($resource, $id)
    {
        [$statusCode, $response] = $this->client->get_by_id($resource, $id);
        return response()->json($response, $statusCode);
    }

    public function getByNik($resource, $nik)
    {
        [$statusCode, $response] = $this->client->get_by_nik($resource, $nik);
        return response()->json($response, $statusCode);
    }

    public function token()
    {
        try {
            $token = $this->client->token();
            return response()->json(['token' => substr($token, 0, 50) . '...', 'status' => 'ok']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createEncounter(Request $request)
    {
        $data = $request->validate([
            'registration_id' => 'required|string',
            'arrived_at' => 'required|date',
            'in_progress_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
            'method' => 'required|in:RAJAL,IGD,RANAP,HOMECARE,TELEKONSULTASI',
            'subject_id' => 'required|string',
            'subject_name' => 'required|string',
            'participant_id' => 'required|string',
            'participant_name' => 'required|string',
            'location_id' => 'required|string',
            'location_name' => 'required|string',
        ]);

        try {
            $encounter = new Encounter();
            $encounter->addRegistrationId($data['registration_id']);
            $encounter->setArrived($data['arrived_at']);
            if ($data['in_progress_at']) {
                $encounter->setInProgress($data['in_progress_at'], $data['in_progress_at']);
            }
            if ($data['finished_at']) {
                $encounter->setFinished($data['finished_at']);
            }
            $encounter->setConsultationMethod($data['method']);
            $encounter->setSubject($data['subject_id'], $data['subject_name']);
            $encounter->addParticipant($data['participant_id'], $data['participant_name']);
            $encounter->addLocation($data['location_id'], $data['location_name']);

            [$statusCode, $response] = $encounter->post();
            $this->logApiCall('POST', 'Encounter', $encounter->json(), $response);
            return response()->json($response, $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createCondition(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'subject_id' => 'required|string',
            'subject_name' => 'required|string',
            'encounter_id' => 'required|string',
            'clinical_status' => 'nullable|in:active,inactive,resolved',
            'category' => 'nullable|in:Diagnosis,Keluhan',
            'onset' => 'nullable|date',
        ]);

        try {
            $condition = new Condition();
            $condition->addClinicalStatus($data['clinical_status'] ?? 'active');
            $condition->addCategory($data['category'] ?? 'Diagnosis');
            $condition->addCode($data['code']);
            $condition->setSubject($data['subject_id'], $data['subject_name']);
            $condition->setEncounter($data['encounter_id']);
            $condition->setOnsetDateTime($data['onset'] ?? now()->toIso8601String());
            $condition->setRecordedDate(now()->toIso8601String());

            [$statusCode, $response] = $condition->post();
            $this->logApiCall('POST', 'Condition', $condition->json(), $response);
            return response()->json($response, $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createObservation(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|string',
            'subject_name' => 'required|string',
            'encounter_id' => 'required|string',
            'performer_id' => 'nullable|string',
            'code' => 'required|string',
            'value' => 'required',
            'unit' => 'nullable|string',
        ]);

        try {
            $obs = new Observation();
            $obs->setStatus('final');
            $obs->addCategory('vital-signs');
            $obs->addCode($data['code']);
            $obs->setSubject($data['subject_id'], $data['subject_name']);
            if ($data['performer_id']) {
                $obs->setPerformer($data['performer_id']);
            }
            $obs->setEncounter($data['encounter_id']);

            [$statusCode, $response] = $obs->post();
            $this->logApiCall('POST', 'Observation', $obs->json(), $response);
            return response()->json($response, $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPatient($id)
    {
        try {
            $patient = new Patient();
            [$statusCode, $response] = $this->client->get_by_id('Patient', $id);
            return response()->json($response, $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPatientByNik($nik)
    {
        try {
            [$statusCode, $response] = $this->client->get_by_nik('Patient', $nik);
            return response()->json($response, $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createPatient(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|string',
            'name' => 'required|string',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        try {
            $patient = new Patient();
            $patient->addIdentifier('nik', $data['nik']);
            $patient->setName($data['name']);
            if ($data['phone']) {
                $patient->addTelecom($data['phone']);
            }
            $patient->setGender($data['gender']);
            $patient->setBirthDate($data['birth_date']);

            if ($data['address']) {
                $patient->setAddress([
                    'address' => $data['address'],
                    'city' => $data['city'] ?? '',
                    'postalCode' => $data['postal_code'] ?? '',
                    'country' => 'id-ID',
                ]);
            }

            [$statusCode, $response] = $patient->post();
            $this->logApiCall('POST', 'Patient', $patient->json(), $response);
            return response()->json($response, $statusCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function logApiCall($action, $resource, $payload, $response)
    {
        try {
            DB::table('satusehat_log')->insert([
                'action' => $action . ' ' . $resource,
                'url' => '/fhir-r4/v1/' . $resource,
                'payload' => is_string($payload) ? $payload : json_encode($payload),
                'response' => is_string($response) ? $response : json_encode($response),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
        }
    }
}
