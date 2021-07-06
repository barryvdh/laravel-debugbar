if (!Object.values) {
    Object.values = function (obj) {
        let objValues = [];

        for (objProperty in obj) {
            if (obj.hasOwnProperty(objProperty)) {
                objValues.push(obj[objProperty]);
            }
        }

        return objValues;
    }
}