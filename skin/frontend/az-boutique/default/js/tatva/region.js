RegionUpdater = Class.create();
RegionUpdater.prototype =
{
    initialize: function(countryEl, regionTextEl, regionSelectEl, regions, disableAction, zipEl)
    {
        this.countryEl = $(countryEl);
        this.regionTextEl = $(regionTextEl);
        this.regionSelectEl = $(regionSelectEl);
        this.zipEl = $(zipEl);
        this.regions = regions;
        this.disableAction = (typeof disableAction == 'undefined') ? 'hide' : disableAction;
        this.zipOptions = (typeof zipOptions == 'undefined') ? false : zipOptions;
        if (this.regionSelectEl.options.length <= 1)
        {
            this.update();
        }
        Event.observe(this.countryEl, 'change', this.update.bind(this));
    },
    update: function()
    {
        if (this.regions[this.countryEl.value])
        {
            if (this.countryEl.value == 'FR')
            {
                if (document.getElementById('region_id'))
                {
                    document.getElementById('region_id').disabled = true;
                }
            }
            else
            {
                if (document.getElementById('region_id'))
                {
                    document.getElementById('region_id').disabled = false;
                }
                if (document.getElementById('regionfr'))
                {
                    document.getElementById('regionfr').innerHTML = "";
                }
            }
            var i, option, region, def;
            if (this.regionTextEl)
            {
                def = this.regionTextEl.value.toLowerCase();
                this.regionTextEl.value = '';
            }
            if (!def)
            {
                def = this.regionSelectEl.getAttribute('defaultValue');
            }

            this.regionSelectEl.options.length = 1;
            for (regionId in this.regions[this.countryEl.value])
            {
                region = this.regions[this.countryEl.value][regionId];
                option = document.createElement('OPTION');
                option.value = regionId;
                option.text = region.name;
                if (this.regionSelectEl.options.add)
                {
                    this.regionSelectEl.options.add(option);
                }
                else
                {
                    this.regionSelectEl.appendChild(option);
                }
                if (regionId == def || region.name.toLowerCase() == def || region.code.toLowerCase() == def)
                {
                    this.regionSelectEl.value = regionId;
                }
            }
            if (this.disableAction == 'hide')
            {
                if (this.regionTextEl)
                {
                    this.regionTextEl.style.display = 'none';
                }
                this.regionSelectEl.style.display = '';
            }
            else if (this.disableAction == 'disable')
            {
                if (this.regionTextEl)
                {
                    this.regionTextEl.disabled = true;
                }
                this.regionSelectEl.disabled = false;
            }
            this.setMarkDisplay(this.regionSelectEl, true);
        }
        else
        {
            if (this.disableAction == 'hide')
            {
                if (this.regionTextEl)
                {
                    this.regionTextEl.style.display = '';
                }
                this.regionSelectEl.style.display = 'none';
                Validation.reset(this.regionSelectEl);
            }
            else if (this.disableAction == 'disable')
            {
                if (this.regionTextEl)
                {
                    this.regionTextEl.disabled = false;
                }
                this.regionSelectEl.disabled = true;
            }
            else if (this.disableAction == 'nullify')
            {
                this.regionSelectEl.options.length = 1;
                this.regionSelectEl.value = '';
                this.regionSelectEl.selectedIndex = 0;
                this.lastCountryId = '';
            }
            this.setMarkDisplay(this.regionSelectEl, false);
        }
        var zipUpdater = new ZipUpdater(this.countryEl.value, this.zipEl);
        zipUpdater.update();
    },
    setMarkDisplay: function(elem, display)
    {
        elem = $(elem);
        var labelElement = elem.up(0).down('label > span.required') || elem.up(1).down('label > span.required') || elem.up(0).down('label.required > em') || elem.up(1).down('label.required > em');
        if (labelElement)
        {
            inputElement = labelElement.up().next('input');
            if (display)
            {
                labelElement.show();
                if (inputElement)
                {
                    inputElement.addClassName('required-entry');
                }
            }
            else
            {
                labelElement.hide();
                if (inputElement)
                {
                    inputElement.removeClassName('required-entry');
                }
            }
        }
    }
}
StateUpdater = Class.create();
StateUpdater.prototype =
{
    initialize: function(country, region, region_id, zipElement, mycars)
    {
        this.country = $(country);
        this.region_id = $(region_id);
        this.zipElement = $(zipElement);
        Event.observe(this.zipElement, 'keyup', this.update.bind(this));
    },
    update: function()
    {
        if (this.country.value == 'FR')
        {
            var stateval = 0;
            var zipcode = this.zipElement.value;


            var zipstr = zipcode.substr(0, 3);

            if (zipstr == 971)
            {
                stateval = mycars[0];
            }
            else if (zipstr == 972)
            {
                stateval = mycars[1];
            }
            else if (zipstr == 973)
            {
                stateval = mycars[2];
            }
            else if (zipstr == 974)
            {
                stateval = mycars[3];
            }
            else if (zipstr == 975)
            {
                stateval = mycars[4];
            }
            else if (zipstr == 976)
            {
                stateval = mycars[5];
            }
            else if (zipstr == 984)
            {
                stateval = mycars[6];
            }
            else if (zipstr == 986)
            {
                stateval = mycars[7];
            }
            else if (zipstr == 987)
            {
                stateval = mycars[8];
            }
            else if (zipstr == 988)
            {
                stateval = mycars[9];
            }
            else if (zipstr == 980)
            {
                stateval = mycars[10];
            }


            var zipstrtwo = zipcode.substr(0, 2);
            if (zipstrtwo == '00')
            {
                stateval = 289;
            }
            else if (zipstrtwo == '01')
            {
                stateval = 182;
            }
            else if (zipstrtwo == '02')
            {
                stateval = 183;
            }
            else if (zipstrtwo == '03')
            {
                stateval = 184;
            }
            else if (zipstrtwo == '04')
            {
                stateval = 185;
            }
            else if (zipstrtwo == '06')
            {
                stateval = 187;
            }
            else if (zipstrtwo == '07')
            {
                stateval = 188;
            }
            else if (zipstrtwo == '08')
            {
                stateval = 189;
            }
            else if (zipstrtwo == '09')
            {
                stateval = 190;
            }
            else if (zipstrtwo == '10')
            {
                stateval = 191;
            }
            else if (zipstrtwo == '11')
            {
                stateval = 192;
            }
            else if (zipstrtwo == '12')
            {
                stateval = 193;
            }
            else if (zipstrtwo == '13')
            {
                stateval = 194;
            }
            else if (zipstrtwo == '14')
            {
                stateval = 195;
            }
            else if (zipstrtwo == '15')
            {
                stateval = 196;
            }
            else if (zipstrtwo == '16')
            {
                stateval = 197;
            }
            else if (zipstrtwo == '17')
            {
                stateval = 198;
            }
            else if (zipstrtwo == '18')
            {
                stateval = 199;
            }
            else if (zipstrtwo == '19')
            {
                stateval = 200;
            }
            else if (zipstrtwo == '2A')
            {
                stateval = 201;
            }
            else if (zipstrtwo == '20')
            {
                stateval = 202;
            }
            else if (zipstrtwo == '21')
            {
                stateval = 203;
            }
            else if (zipstrtwo == '22')
            {
                stateval = 204;
            }
            else if (zipstrtwo == '23')
            {
                stateval = 205;
            }
            else if (zipstrtwo == '24')
            {
                stateval = 206;
            }
            else if (zipstrtwo == '25')
            {
                stateval = 207;
            }
            else if (zipstrtwo == '26')
            {
                stateval = 208;
            }
            else if (zipstrtwo == '27')
            {
                stateval = 209;
            }
            else if (zipstrtwo == '28')
            {
                stateval = 210;
            }
            else if (zipstrtwo == '29')
            {
                stateval = 211;
            }
            else if (zipstrtwo == '30')
            {
                stateval = 212;
            }
            else if (zipstrtwo == '32')
            {
                stateval = 214;
            }
            else if (zipstrtwo == '33')
            {
                stateval = 215;
            }
            else if (zipstrtwo == '34')
            {
                stateval = 216;
            }
            else if (zipstrtwo == '35')
            {
                stateval = 217;
            }
            else if (zipstrtwo == '36')
            {
                stateval = 218;
            }
            else if (zipstrtwo == '37')
            {
                stateval = 219;
            }
            else if (zipstrtwo == '38')
            {
                stateval = 220;
            }
            else if (zipstrtwo == '39')
            {
                stateval = 221;
            }
            else if (zipstrtwo == '40')
            {
                stateval = 222;
            }
            else if (zipstrtwo == '41')
            {
                stateval = 223;
            }
            else if (zipstrtwo == '42')
            {
                stateval = 224;
            }
            else if (zipstrtwo == '44')
            {
                stateval = 226;
            }
            else if (zipstrtwo == '45')
            {
                stateval = 227;
            }
            else if (zipstrtwo == '46')
            {
                stateval = 228;
            }
            else if (zipstrtwo == '47')
            {
                stateval = 229;
            }
            else if (zipstrtwo == '48')
            {
                stateval = 230;
            }
            else if (zipstrtwo == '49')
            {
                stateval = 231;
            }
            else if (zipstrtwo == '50')
            {
                stateval = 232;
            }
            else if (zipstrtwo == '51')
            {
                stateval = 233;
            }
            else if (zipstrtwo == '53')
            {
                stateval = 235;
            }
            else if (zipstrtwo == '54')
            {
                stateval = 236;
            }
            else if (zipstrtwo == '55')
            {
                stateval = 237;
            }
            else if (zipstrtwo == '56')
            {
                stateval = 238;
            }
            else if (zipstrtwo == '57')
            {
                stateval = 239;
            }
            else if (zipstrtwo == '58')
            {
                stateval = 240;
            }
            else if (zipstrtwo == '59')
            {
                stateval = 241;
            }
            else if (zipstrtwo == '60')
            {
                stateval = 242;
            }
            else if (zipstrtwo == '61')
            {
                stateval = 243;
            }
            else if (zipstrtwo == '62')
            {
                stateval = 244;
            }
            else if (zipstrtwo == '63')
            {
                stateval = 245;
            }
            else if (zipstrtwo == '64')
            {
                stateval = 246;
            }
            else if (zipstrtwo == '66')
            {
                stateval = 248;
            }
            else if (zipstrtwo == '69')
            {
                stateval = 251;
            }
            else if (zipstrtwo == '71')
            {
                stateval = 253;
            }
            else if (zipstrtwo == '72')
            {
                stateval = 254;
            }
            else if (zipstrtwo == '73')
            {
                stateval = 255;
            }
            else if (zipstrtwo == '77')
            {
                stateval = 259;
            }
            else if (zipstrtwo == '80')
            {
                stateval = 262;
            }
            else if (zipstrtwo == '81')
            {
                stateval = 263;
            }
            else if (zipstrtwo == '82')
            {
                stateval = 264;
            }
            else if (zipstrtwo == '83')
            {
                stateval = 265;
            }
            else if (zipstrtwo == '84')
            {
                stateval = 266;
            }
            else if (zipstrtwo == '85')
            {
                stateval = 267;
            }
            else if (zipstrtwo == '86')
            {
                stateval = 268;
            }
            else if (zipstrtwo == '88')
            {
                stateval = 270;
            }
            else if (zipstrtwo == '89')
            {
                stateval = 271;
            }
            else if (zipstrtwo == '67')
            {
                stateval = 249;
            }
            else if (zipstrtwo == '79')
            {
                stateval = 261;
            }
            else if (zipstrtwo == '91')
            {
                stateval = 273;
            }
            else if (zipstrtwo == '31')
            {
                stateval = 213;
            }
            else if (zipstrtwo == '43')
            {
                stateval = 225;
            }
            else if (zipstrtwo == '52')
            {
                stateval = 234;
            }
            else if (zipstrtwo == '70')
            {
                stateval = 252;
            }
            else if (zipstrtwo == '74')
            {
                stateval = 256;
            }
            else if (zipstrtwo == '87')
            {
                stateval = 269;
            }
            else if (zipstrtwo == '65')
            {
                stateval = 247;
            }
            else if (zipstrtwo == '92')
            {
                stateval = 274;
            }
            else if (zipstrtwo == '75')
            {
                stateval = 257;
            }
            else if (zipstrtwo == '76')
            {
                stateval = 258;
            }
            else if (zipstrtwo == '93')
            {
                stateval = 275;
            }
            else if (zipstrtwo == '90')
            {
                stateval = 272;
            }
            else if (zipstrtwo == '95')
            {
                stateval = 277;
            }
            else if (zipstrtwo == '94')
            {
                stateval = 276;
            }
            else if (zipstrtwo == '78')
            {
                stateval = 260;
            }
            else if (zipstrtwo == '68')
            {
                stateval = 250;
            }
            else if (zipstrtwo == '05')
            {
                stateval = 186;
            }

            if(zipcode == "97150")
            {
                stateval = mycars[11];
            }
            else if(zipcode == "97133")
            {
                stateval = mycars[12];
            }
            this.region_id.value = stateval;
        }
    },
}
