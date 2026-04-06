<?php

namespace App\Services\Integration;

/**
 * Validates FHIR Resource structures before sending to SATUSEHAT.
 * Requirements: 14.8
 */
class FhirValidator
{
    public function validate(array $resource): array
    {
        if (empty($resource['resourceType'])) {
            return ['valid' => false, 'errors' => ['resourceType wajib diisi']];
        }

        return match ($resource['resourceType']) {
            'Patient'           => $this->validatePatient($resource),
            'Encounter'         => $this->validateEncounter($resource),
            'Condition'         => $this->validateCondition($resource),
            'MedicationRequest' => $this->validateMedicationRequest($resource),
            'Observation'       => $this->validateObservation($resource),
            default             => ['valid' => true, 'errors' => []],
        };
    }

    public function validatePatient(array $resource): array
    {
        $errors = [];
        if (empty($resource['identifier']) || !is_array($resource['identifier'])) $errors[] = 'Patient.identifier wajib diisi';
        if (empty($resource['name']) || !is_array($resource['name'])) $errors[] = 'Patient.name wajib diisi';
        if (empty($resource['gender'])) $errors[] = 'Patient.gender wajib diisi';
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public function validateEncounter(array $resource): array
    {
        $errors = [];
        if (empty($resource['status'])) $errors[] = 'Encounter.status wajib diisi';
        if (empty($resource['class'])) $errors[] = 'Encounter.class wajib diisi';
        if (empty($resource['subject']['reference'])) $errors[] = 'Encounter.subject.reference wajib diisi';
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public function validateCondition(array $resource): array
    {
        $errors = [];
        if (empty($resource['clinicalStatus'])) $errors[] = 'Condition.clinicalStatus wajib diisi';
        if (empty($resource['code']['coding']) || !is_array($resource['code']['coding'])) $errors[] = 'Condition.code.coding wajib diisi';
        if (empty($resource['subject']['reference'])) $errors[] = 'Condition.subject.reference wajib diisi';
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public function validateMedicationRequest(array $resource): array
    {
        $errors = [];
        if (empty($resource['status'])) $errors[] = 'MedicationRequest.status wajib diisi';
        if (empty($resource['intent'])) $errors[] = 'MedicationRequest.intent wajib diisi';
        if (empty($resource['medicationCodeableConcept'])) $errors[] = 'MedicationRequest.medicationCodeableConcept wajib diisi';
        if (empty($resource['subject']['reference'])) $errors[] = 'MedicationRequest.subject.reference wajib diisi';
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public function validateObservation(array $resource): array
    {
        $errors = [];
        if (empty($resource['status'])) $errors[] = 'Observation.status wajib diisi';
        if (empty($resource['code'])) $errors[] = 'Observation.code wajib diisi';
        if (empty($resource['subject']['reference'])) $errors[] = 'Observation.subject.reference wajib diisi';
        return ['valid' => empty($errors), 'errors' => $errors];
    }
}
