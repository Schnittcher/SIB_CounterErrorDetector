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
            $CounterSerialID = IPS_GetObjectIDByIdent("Value", $this->ReadPropertyInteger("CounterSerialNumber"));
            SetValue($this->GetIDForIdent("CounterSerialNumber"), GetValue($CounterSerialID));
            $this->RegisterMessage($CounterSerialID, 10603 /* VM_UPDATE */ );
        }

        if ($this->ReadPropertyInteger("CounterState") != 0) {
            $CounterStateID = IPS_GetObjectIDByIdent("Value", $this->ReadPropertyInteger("CounterState"));
            $this->RegisterMessage($CounterStateID, 10603 /* VM_UPDATE */ );
        }

        $this->CheckCounter();
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->CheckCounter();
    }

    private function CheckCounter()
    {
        $Error = FALSE;
        // check Serial Number
        $CounterSerialNumberSaved = GetValue($this->GetIDForIdent("CounterSerialNumber"));
        $CounterSerialNumberNow   = GetValue(IPS_GetObjectIDByIdent("Value", $this->ReadPropertyInteger("CounterSerialNumber")));

        if ($CounterSerialNumberSaved != $CounterSerialNumberNow) {
            $Error = TRUE;
        }

        // check State
        $CounterStateID = GetValue(IPS_GetObjectIDByIdent("Value", $this->ReadPropertyInteger("CounterState")));

        if ($CounterStateID ) {
            $Error = TRUE;
        }

        // Set Error
        if ($Error){
          SetValue($this->GetIDForIdent("CounterError"), TRUE);
        }
        else {
            SetValue($this->GetIDForIdent("CounterError"), FALSE);
        }
    }
}
