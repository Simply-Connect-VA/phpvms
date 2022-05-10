<script type="text/javascript">
  // Read selection value (json string) and send the results to proper form fields
  // Also re-arrange acdata json string for SimBrief
  const paxwgt = Math.round({{ $pax_weight }} + {{ $bag_weight }});
  const paxfig = Number({{ $tpaxfig ?? 0 }})
  const unitwgt = String("{{ setting('units.weight') }}");
  const kgstolbs = Number(2.20462262185);
  const rvr = String("{{ $sb_rvr ?? '500' }}");
  const actype = String("{{ $aircraft->subfleet->simbrief_type ?? $aircraft->icao }}");
  const rmktext = String("{{ $sb_rmk ?? config('app.name') }}").toUpperCase();

  // Convert weights according to PhpVms settings for visual display
  function ConvertWeight(weight_value = null, weight_target = unitwgt) {
    if (weight_target === 'kg') {
      weight_value = ((weight_value / kgstolbs) * 1000).toFixed(0);
    } else {
      weight_value = (weight_value * 1000).toFixed(0);
    }
    return weight_value;
  }

  // Set Visible fields and final acdata json string for SimBrief
  function ChangeSpecs() {
    let str = document.getElementById("addon").value;
    if (str === "0") {
      // Nothing selected remove Spec Fields and use PhpVms data for basics
      document.getElementById("dow").value = "--";
      document.getElementById("mzfw").value = "--";
      document.getElementById("mtow").value = "--";
      document.getElementById("mlw").value = "--";
      document.getElementById("maxfuel").value = "--";
      document.getElementById("fuelfactor").value = "";
      document.getElementById("type").value = actype;
      document.getElementById("acdata").value = "{'extrarmk':'RVR\/" + rvr +" RMK\/TCAS " + rmktext + "','paxwgt':" + paxwgt + "}";
      document.getElementById("tdPayload").title = "Calculation Not Possible !";
    } else {
      // A specification is selected, proceed working on it
      // Change selected Json String to Json Object
      let AcDataJson = JSON.parse(str);
      // Get custom airframe id from specs
      if (typeof AcDataJson.airframe_id != "undefined") {
        document.getElementById("type").value = AcDataJson.airframe_id;
        delete AcDataJson.airframe_id;
      }
      // Get fuel factor from specs
      if (typeof AcDataJson.fuelfactor != "undefined") {
        document.getElementById("fuelfactor").value = AcDataJson.fuelfactor;
        delete AcDataJson.fuelfactor;
      }
      // Populate visible fields with avilable data
      if (typeof AcDataJson.oew != "undefined") {
        document.getElementById("dow").value = ConvertWeight(AcDataJson.oew);
      } else {
        document.getElementById("dow").value = "--";
      }
      if (typeof AcDataJson.mzfw != "undefined") {
        document.getElementById("mzfw").value = ConvertWeight(AcDataJson.mzfw);
      } else {
        document.getElementById("mzfw").value = "--";
      }
      if (typeof AcDataJson.mtow != "undefined") {
        document.getElementById("mtow").value = ConvertWeight(AcDataJson.mtow);
      } else {
        document.getElementById("mtow").value = "--";
      }
      if (typeof AcDataJson.mlw != "undefined") {
        document.getElementById("mlw").value = ConvertWeight(AcDataJson.mlw);
      } else {
        document.getElementById("mlw").value = "--";
      }
      if (typeof AcDataJson.maxfuel != "undefined") {
        document.getElementById("maxfuel").value = ConvertWeight(AcDataJson.maxfuel);
      } else {
        document.getElementById("maxfuel").value = "--";
      }
      // Provide a clue about possible ZFW Unlerload
      if (typeof AcDataJson.oew != "undefined" && typeof AcDataJson.mzfw != "undefined") {
        document.getElementById("tdPayload").title = "ZFW Underload: " + String((ConvertWeight(AcDataJson.mzfw) - ConvertWeight(AcDataJson.oew)) - Number({{ $tpayload }})) + " " + unitwgt;
      } else {
        document.getElementById("tdPayload").title = "Calculation Not Possible !";
      }
      // Use Specs PAXWGT or PhpVms PAXWGT
      if (typeof AcDataJson.paxwgt != "undefined") {
        if (paxfig > 0) {
          document.getElementById("tdPaxLoad").title = "Spec Pax Load: " + Number(AcDataJson.paxw * paxfig) + " " + unitwgt;
          document.getElementById("tdBagLoad").title = "Spec Bag Load: " + Number(AcDataJson.bagw * paxfig) + " " + unitwgt;
        }
        delete AcDataJson.paxw;
        delete AcDataJson.bagw;
      } else {
        AcDataJson.paxwgt = paxwgt;
      }

      // Write final ACDATA field for SimBrief
      document.getElementById("acdata").value = JSON.stringify(AcDataJson);
    }
  }
</script>
