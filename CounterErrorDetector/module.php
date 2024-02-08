<?php
// Klassendefinition
class CounterErrorDetector extends IPSModule
{
    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create()
    {
        // Diese Zeile nicht löschen.
        parent::Create();

        $this->RegisterPropertyInteger("CounterSerialNumber", 0);
        $this->RegisterPropertyInteger("CounterState", 0);

        $this->RegisterVariableInteger("CounterSerialNumber", "Counter Serial Number", "", 1);
        $this->RegisterVariableBoolean("CounterState", "Counter State", "", 2);
        $this->RegisterVariableBoolean("CounterError", "Counter Error", "~Alert", 3);
    }
    // Überschreibt die intere IPS_ApplyChanges($id) Funktion
    public function ApplyChanges()
    {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        if ($this->ReadPropertyInteger("CounterSerialNumber") != 0) {
            $CounterSerialID = $this->ReadPropertyInteger("CounterSerialNumber");
            $this->SetValue('CounterSerialNumber',GetValue($CounterSerialID));
            $this->RegisterMessage($CounterSerialID, VM_UPDATE);
        }

        if ($this->ReadPropertyInteger("CounterState") != 0) {
            $CounterStateID = $this->ReadPropertyInteger("CounterState");
            $this->SetValue('CounterState', GetValue($CounterStateID));
            $this->RegisterMessage($CounterStateID, VM_UPDATE);
        }
        $this->CheckCounter();
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        //Nur ausführen, wenn sich die Variable verändert hat.
        if ($Data[1]) {
            $this->CheckCounter();
        }
    }

    private function CheckCounter()
    {
        $Error = FALSE;

        // check Serial Number
        if ($this->ReadPropertyInteger("CounterSerialNumber") != 0){
          $CounterSerialNumberSaved = $this->GetValue('CounterSerialNumber');
          $CounterSerialNumberNow   = GetValue($this->ReadPropertyInteger("CounterSerialNumber"));

          if ($CounterSerialNumberSaved != $CounterSerialNumberNow) {
              $Error = true;
          }
        }

        // check State
        if ($this->ReadPropertyInteger("CounterState") != 0){
          $CounterState = GetValue($this->ReadPropertyInteger("CounterState"));
          $this->SetValue('CounterState', $CounterState);

          $this->SendDebug("CounterErrorDetector", "CounterState " . (int)$CounterState, 0);

          if (!$CounterState ) {
              $Error = true;
          }
        }

        // Set Error
        if ($Error){
          $this->SetValue('CounterError', true);
        }
        else {
            $this->SetValue('CounterError', false);
        }
    }
}
