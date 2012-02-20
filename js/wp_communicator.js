function load() {
    var display = new Garmin.DeviceDisplay("garminDisplay", {
        pathKeyPairsArray: ["http://example.com", "MY_KEY"],
        unlockOnPageLoad: false,
        hideIfBrowserNotSupported: true,
        showStatusElement: false,
        autoFindDevices: false,
        findDevicesButtonText: "Download to GPS",
        showCancelFindDevicesButton: false,
        showDeviceSelectOnLoad: false,
        showDeviceSelectNoDevice: false,
        autoReadData: false,
        autoWriteData: true,
        showReadDataElement: false,
        useLinks: false,
        getWriteData: function() { return $("dataString").value; },
        getWriteDataFileName: function() { return gpxFileName; },
        afterFinishWriteToDevice: function() { alert("Transfer complete"); }
    });
}