<?php
  // Klassendefinition
  class CounterErrorDetector extends IPSModule {
      // Überschreibt die interne IPS_Create($id) Funktion
      public function Create() {
          // Diese Zeile nicht löschen.
          parent::Create();

          $this->RegisterPropertyInteger("CounterSerialNumber", 0);
          $this->RegisterPropertyInteger("CounterState", 0);

          $this->RegisterVariableInteger("CounterSerialNumber", "CounterSerialNumber", "", 1);
          $this->RegisterVariableBoolean("CounterState", "CounterState", "", 2);
          $this->RegisterVariableBoolean("CounterError", "CounterError", "", 3);
      }
      // Überschreibt die intere IPS_ApplyChanges($id) Funktion
      public function ApplyChanges() {
          // Diese Zeile nicht löschen
          parent::ApplyChanges();

          $CounterSerialID = IPS_GetObjectIDByIdent("Value", $this->ReadPropertyInteger("CounterSerialNumber"));
          $CounterStateID = IPS_GetObjectIDByIdent("Value", $this->ReadPropertyInteger("CounterState"));

          $this->RegisterMessage($CounterSerialID, 10603 /* VM_UPDATE */);
          $this->RegisterMessage($CounterStateID, 10603 /* VM_UPDATE */);
          
      }

      public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
            $this->CheckSerialNumber();
            $NewCounterSerialNumber = GetValue($this->ReadPropertyInteger("CounterSerialNumber"));
            SetValue($this->GetIDForIdent("CounterSerialNumber"), $NewCounterSerialNumber);
      }

      private function CheckSerialNumber(){
        $CounterSerialNumberSaved = GetValue($this->ReadPropertyInteger("CounterSerialNumber"));
        $CounterSerialNumberNow = GetValue(ReadPropertyInteger("CounterSerialNumber"));

        if ($CounterSerialNumberSaved != $CounterSerialNumberNow){
          SetValue($this->GetIDForIdent("CounterError"), TRUE);
        }else{
          SetValue($this->GetIDForIdent("CounterError"), FALSE);
        }
      }
  }
