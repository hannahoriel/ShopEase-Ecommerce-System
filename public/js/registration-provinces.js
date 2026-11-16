window.loadRegistrationProvinces = async function (region, load, savedProvince = '') {
    let code = region.selectedOptions[0]?.dataset.regionCode;
    let provinces;

    if (!code && savedProvince) {
        provinces = await load('provinces');
        if (region.value) return [];
        const match = provinces.find(province =>
            (province.name ?? province.prov_name)?.toLowerCase() === savedProvince.toLowerCase()
        );
        const prefix = String(match?.code ?? match?.psgc_code ?? match?.prov_code ?? '').slice(0, 2);
        const option = Array.from(region.options).find(item => item.dataset.regionCode === prefix);
        if (option) {
            region.value = option.value;
            code = prefix;
        }
    }

    if (!code) return [];
    if (code === '13') {
        provinces ??= await load('provinces');
        return provinces.filter(province => province.isRegion === true || String(province.code) === '130000000');
    }

    return load('regions/' + code + '0000000/provinces');
};
