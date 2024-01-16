<?php
  // Klassendefinition
  class CounterErrorDetector extends IPSModule {
      // Überschreibt die interne IPS_Create($id) Funktion
      public function Create() {
          // Diese Zeile nicht löschen.
          parent::Create();

          $this->RegisterPropertyInteger("CounterSerialNumber", 0);
          $this->RegisterPropertyBoolean("CounterStatus", 0);

          $this->RegisterVariableInteger("CounterSerialNumber", "CounterSerialNumber", "", 1);
          $this->RegisterVariableBoolean("CounterStatus", "CounterStatus", "", 2);
          $this->RegisterVariableBoolean("CounterError", "CounterError", "", 3);
      }
      // Überschreibt die intere IPS_ApplyChanges($id) Funktion
      public function ApplyChanges() {
          // Diese Zeile nicht löschen
          parent::ApplyChanges();

          $this->RegisterMessage($this->ReadPropertyInteger("CounterSerialNumber"), 10603 /* VM_UPDATE */);
          $this->RegisterMessage($this->ReadPropertyBoolean("CounterStatus"), 10603 /* VM_UPDATE */);


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
